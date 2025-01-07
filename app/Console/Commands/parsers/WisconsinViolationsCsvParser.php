<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class WisconsinViolationsCsvParser extends Command
{
    protected $signature = 'custom:parse-wisconsin-violations-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/wisconsin/WisconsinViolations.csv';

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
                                ->where('state', 'WI')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime($data[1]));

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_type', $data[3])
                                        ->where('report_date', $reportDate)
                                        ->where('report_status', $data[2])
                                        ->first();

                if (!$inspection) {
                    $newCount++;
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->inserted = date('Y-m-d H:i:s');

                    $this->info("new record $row " . $data[0] . " - " . $data[1]);

                } else {
                    $existCount++;
                }
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->report_date = $reportDate;
                $inspection->report_status = $data[2];
                $inspection->report_type = $data[3];
                $inspection->rule_description = $data[4];
                $inspection->state = 'WI';

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