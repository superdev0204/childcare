<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class MichiganInspectionsParser extends Command
{
    protected $signature = 'custom:parse-michigan-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/michigan/MichiganInspections.csv';

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'MI')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_url', $data[3])
                                        ->first();

                if (!$inspection) {
                    $newCount++;
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;

                    if ($data[1] <> "") {
                        $inspection->report_date = date('Y-m-d', strtotime($data[1]));
                    }

                    $inspection->report_type = $data[2];
                    $inspection->report_url = $data[3];
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $existCount++;
                    $this->info("exist record $row " . $data[0] . " - " . $data[2] . " - " . $data[3]);
                }
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->state = 'MI';

                if (isset($inspection->id)) {
                    $inspection->save();
                } else {
                    DB::table('inspections')->insert((array) $inspection);
                }
            }
        }

        $this->info("$existCount exists; $newCount new; $skipCount skipped");

        fclose($handle);
    }
}