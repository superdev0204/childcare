<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class ConnecticutViolationsCsvParser extends Command
{
    protected $signature = 'custom:parse-connecticut-violations-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/connecticut/ConnecticutViolations.csv';

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'CT')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);

            } else {
                $reportDate = date('Y-m-d', strtotime($data[3]));

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_type', $data[2])
                                        ->where('report_date', $reportDate)
                                        ->where('report_status', $data[1])
                                        ->first();

                if (!$inspection) {
                    $newCount++;
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->report_date = $reportDate;
                    $inspection->rule_description = str_replace("[Reference regulation link above.]", "", $data[4]);
                    $inspection->rule_description = str_replace(")]", ")]<br/>", $inspection->rule_description);
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $this->info("exist record $row " . $data[0] . " - " . $data[2]);
                    $existCount++;
                    $inspection->facility_id = $facility->id;
                    $inspection->report_date = $reportDate;
                    $inspection->rule_description = str_replace("[Reference regulation link above.]", "", $data[4]);
                    $inspection->rule_description = str_replace(")]", ")]<br/>", $inspection->rule_description);
                }
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->state = 'CT';
                $inspection->report_status = $data[1];
                $inspection->report_type = $data[2];
                $inspection->current_status = $data[5];

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