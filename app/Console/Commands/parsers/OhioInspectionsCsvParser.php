<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class OhioInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-ohio-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/ohio/OhioInspections.csv';

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

            $facility = Facility::where('operation_id', $data[0])
                                ->where('state', 'OH')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('state_id', $data[0])
                                    ->where('state', 'OH')
                                    ->first();
            }

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime($data[1]));
                $statusDate = date('Y-m-d', strtotime($data[5]));
                $reportStatus = $data[3];

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_type', $data[2])
                                        ->where('report_date', $reportDate)
                                        ->first();

                if (!$inspection) {
                    $newCount++;
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $this->info("exist record $row " . $data[0] . " - " . $data[3]);
                    $existCount++;
                }

                $inspection->report_date = $reportDate;
                $inspection->report_status = $reportStatus;
                $inspection->report_type = $data[2];
                $inspection->status_date = $statusDate;
                $inspection->current_status = $data[4];
                $inspection->report_url = $data[6];
                $inspection->state='OH';
                $inspection->updated = date('Y-m-d H:i:s');
                
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