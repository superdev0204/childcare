<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class ArkansasInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-arkansas-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/arkansas/ArkansasInspections.csv';

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 3000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'AR')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime($data[1]));

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_type', 'Facility Visit')
                                        ->where('report_date', $reportDate)
                                        ->first();

                if (!$inspection) {
                    $newCount++;
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->report_date = $reportDate;
                    $inspection->report_type = 'Facility Visit';
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $this->info("exist record $row " . $data[0] . " - " . $data[1]);
                    $existCount++;
                }
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->rule_description = $data[2];
                $inspection->state = 'AR';

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