<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class UtahInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-utah-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/utah/UtahCCInspections.csv';

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
                                ->where('state', 'UT')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime($data[1]));

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_type', $data[2])
                                        ->where('report_date', $reportDate)
                                        ->where(function ($query) use ($data) {
                                            $query->whereNull('rule_description')
                                                ->orWhere('rule_description', 'LIKE', '%' . $data[4] . '%');
                                        })
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
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->report_date = $reportDate;
                $inspection->report_type = $data[2];

                if ($data[3] <> '') {
                    $inspection->complaint_date = date('Y-m-d', strtotime($data[3]));
                }

                if ($data[4] <> '') {
                    $inspection->rule_description = $data[4] . "\n";
                }

                if ($data[9] <> '') {
                    $inspection->rule_description .= '<p> ' . $data[9] . ' </p>';
                }

                if ($data[5] <> '') {
                    $inspection->report_status = $data[5] . " - " . $data[6];
                }

                if ($data[7] <> '') {
                    $inspection->status_date = date('Y-m-d', strtotime($data[7]));
                }

                if ($data[8] <> '') {
                    $inspection->current_status = $data[8];
                }

                $inspection->state = 'UT';

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