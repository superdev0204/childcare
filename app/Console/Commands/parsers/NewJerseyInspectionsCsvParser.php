<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class NewJerseyInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-new-jersey-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/new-jersey/NewJerseyViolationsNew.csv';

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 2000, ";")) !== FALSE) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'NJ')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                
                $reportDate = date('Y-m-d', strtotime($data[4]));

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_type', $data[0])
                                        ->where('report_date', $reportDate)
                                        ->where('report_status', $data[8])
                                        ->first();

                if (!$inspection) {
                    $newCount++;
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $this->info("exist record $row " . $facility->id . " - " . $data[5] . " - " . $data[0]);
                    $existCount++;
                }
                
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->report_type = $data[0];
                $inspection->report_date = $reportDate;
                $inspection->report_status = $data[8];
                $inspection->rule_description = $data[6];
                
                if ($data[2] <> "") {
                	$inspection->current_status = substr($data[2],0, 5000);
                }
                if ($data[3] <> "") {
                    $inspection->status_date = date('Y-m-d', strtotime($data[3]));
                }
                
                $inspection->state='NJ';

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