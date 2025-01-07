<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class DelawareInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-delaware-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/delaware/DelawareNonCompliance.csv';

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

            if ($row == 1 || $data[8]=="Facility Visit") {
                continue;
            }

            $facility = Facility::where('approved', '>', 0)
                                ->where('state_id', $data[0])
                                ->where('state', 'DE')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime(str_replace('-', '/', $data[1])));

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_type', $data[8])
                                        ->where('report_status', 'LIKE', $data[2] . '%')
                                        ->where('report_date', $reportDate)
                                        ->first();

                if (!$inspection) {
                    $newCount++;
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->report_date = $reportDate;
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $this->info("exist record $row " . $data[0] . " - " . $data[2]);
                    $existCount++;
                }
                $inspection->report_type = $data[8];
                $inspection->report_status = $data[2] . " - " . $data[3];
                $inspection->current_status = $data[4];
                $inspection->status_date = date('Y-m-d', strtotime(str_replace('-', '/', $data[5])));
                $inspection->rule_description = $data[7];
                $inspection->state = 'DE';
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