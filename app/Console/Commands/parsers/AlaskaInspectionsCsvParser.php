<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class AlaskaInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-alaska-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/alaska/AlaskaInspections.csv';

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

            $facility = Facility::where('state_id', trim($data[1]))
                                ->where('state', 'AK')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime($data[3]));
                $violationDate = date('Y-m-d', strtotime($data[5]));
                $complianceDate = date('Y-m-d', strtotime($data[6]));

                $inspection = Inspections::where('facility_id', $facility->id)
                                    ->where('report_type', $data[2])
                                    ->where('report_date', $reportDate)
                                    ->where('report_status', $data[4])
                                    ->where('report_url', $data[0])
                                    ->first();

                if (!$inspection) {
                    $newCount++;

                    $this->info("new record $newCount");

                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->report_type = $data[2];
                    $inspection->report_status = $data[4];
                    $inspection->current_status = $data[7];
                    $inspection->rule_description = "Statute/Regulation: " . $data[8] . "; Section: " . $data[9];
                    $inspection->report_url = $data[0];
                    $inspection->report_date = $reportDate;
                    if ($data[5] <> "") {
                        $inspection->complaint_date = $violationDate;
                    }
                    if ($data[6] <> "") {
                        $inspection->status_date = $complianceDate;
                    }
                    
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $this->info("exist record $row " . $data[0]);
                    $existCount++;
                }
                
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->state = 'AK';

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