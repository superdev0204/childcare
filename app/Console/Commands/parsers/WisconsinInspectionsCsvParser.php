<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class WisconsinInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-wisconsin-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/wisconsin/WisconsinLicensing.csv';

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
            if (trim($data[3]) == "See Violations below") {
                $skipCount++;
                $this->info("skip record $row " . $data[0] . " " . $data[3]);
                continue;
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'WI')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime($data[2]));

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_date', $reportDate)
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
                $inspection->report_status = $data[1];
                $inspection->report_type = $data[3];
                if ($data[4] <> '') {
                    $inspection->report_url = str_replace("../", "https://childcarefinder.wisconsin.gov/", $data[4]);
                }
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