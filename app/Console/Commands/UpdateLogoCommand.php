<?php

namespace App\Console\Commands;

use App\Models\Facility;
use Illuminate\Console\Command;

class UpdateLogoCommand extends Command
{
    protected $signature = 'custom:update-logo {--limit=} {--id=}';

    public function handle()
    {
        $query = Facility::selectRaw('images.provider_id, GROUP_CONCAT(images.imagename ORDER BY images.created desc) as logo')
                        ->join('images', 'images.provider_id', '=', 'facility.id')
                        ->where('images.type', 'LOGO')
                        ->where('images.approved', 1)
                        ->where(function ($query) {
                            $query->where('facility.logo', '')
                                ->orWhereNull('facility.logo');
                        })
                        ->groupBy('images.provider_id');

        if ($this->option('id')) {
            $query->where('images.provider_id', $this->option('id'));
        }

        if ($this->option('limit')) {
            $query->limit($this->option('limit'));
        }

        $rows = $query->get();

        foreach ($rows as $row) {
            $providerId = $row->provider_id;
            $logo = explode(',', $row->logo)[0];

            $this->info($providerId . ' - ' . $logo);

            Facility::where('id', $providerId)->update(['logo' => $logo, 'ludate' => now()]);
        }

        $this->info('Rows updated - ' . count($rows));
    }
}
