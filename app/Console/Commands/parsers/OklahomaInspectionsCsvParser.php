<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class OklahomaInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-oklahoma-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/oklahoma/OklahomaInspections.csv';

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
                                ->where('state', 'OK')
                                ->first();

            if ($facility == null) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime($data[1]));

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_date', $reportDate)
                                        ->where('report_type', 'LIKE', $data[2] . '%')
                                        ->where('report_status', 'LIKE', $data[3])
                                        ->where(function ($query) use ($data) {
                                            $query->whereNull('rule_description')
                                                ->orWhere('rule_description', 'LIKE', $data[4] . '%');
                                        })
                                        ->first();

                if (!$inspection) {
                    $newCount++;
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $this->info("exist record $row " . $data[0] . " - " . $data[6]);
                    $existCount++;
                }
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->report_date = $reportDate;
                $inspection->report_type = $data[2] . ' Inspection';
                $inspection->report_status = $data[3];

                if ($data[6] == "No non-compliances observed") {
                    $inspection->rule_description = $data[6];
                } else {
                    if ($data[4] <> '') {
                        $inspection->rule_description = $data[4];
                    }
                    
                    if ($data[5] <> '') {
                        $inspection->rule_description .= ' - ' . $data[5] . "\n";
                    }
                    
                    if ($data[6] <> '') {
                        if ($inspection->rule_description <> '') {
                            $inspection->rule_description .= ' <p> ' . $data[6] . '</p>';
                        } else {
                            $inspection->rule_description = $data[6];
                        }
                    }
                }
                
                $inspection->current_status = $data[7];

                if ($data[8] <> '') {
                    $inspection->status_date = date('Y-m-d', strtotime($data[8]));
                }

                $inspection->state = 'OK';

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