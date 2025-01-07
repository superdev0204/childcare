<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class VermontInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-vermont-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/vermont/VermontCCViolations.csv';

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

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'VT')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);

            } else {
                $reportDate = date('Y-m-d', strtotime($data[3]));

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_type', $data[1])
                                        ->where('report_date', $reportDate)
                                        ->where('rule_description', 'LIKE', '%' . substr($data[2], 0, 8) . '%')
                                        ->first();

                if (!$inspection) {
                    $newCount++;
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->report_type = $data[1];
                    $inspection->rule_description = $data[2];
                    $inspection->report_date = $reportDate;

                    if ($data[4] <> "") {
                        $inspection->complaint_date = date('Y-m-d', strtotime($data[4]));
                    }

                    if ($data[5] <> "") {
                        $inspection->status_date = date('Y-m-d', strtotime($data[5]));
                    }

                    $inspection->report_status = $data[6];
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $this->info("exist record $row " . $data[0] . " - " . $data[3]);
                    $existCount++;
                }
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->state = 'VT';

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