<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class ArizonaInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-arizona-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/arizona/ArizonaViolations.csv';

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 2000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $facility = Facility::whereRaw('REPLACE(state_id, "-", "") = ?', [trim($data[0])])
                                ->where('state', 'AZ')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime($data[1]));

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_type', $data[3])
                                        ->where('report_date', $reportDate)
                                        ->where('report_status', $data[4])
                                        ->first();

                if (!$inspection) {
                    $newCount++;

                    $this->info("new record $newCount");

                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $this->info("exist record $row " . $data[0]);
                    $existCount++;
                }
                $inspection->report_status = $data[4];
                $inspection->report_date = $reportDate;
                $inspection->status_date = date('Y-m-d', strtotime($data[2]));
                $inspection->report_type = $data[3];
                $inspection->rule_description = $data[5];
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->state = 'AZ';

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