<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class CaliforniaInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-california-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/california/CaliforniaChildCare.csv';

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 4000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'CA')
                                ->first();

            if ($facility == null || $data[18] == "") {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);

            } else {
                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_type', 'Summary')
                                        ->first();

                if (!$inspection) {
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->inserted = date('Y-m-d H:i:s');
                    $newCount++;
                } else {
                    if ($data[18] <= $inspection->pages) {
                        $existCount++;
                        $this->info("exist record $row " . $data[0] . " - " . $data[18]);
                    }
                }

                $inspection->state = 'CA';
                $inspection->pages = $data[18];
                $inspection->report_type = 'Summary';
                $inspection->report_status = $data[19];
                $inspection->updated = date('Y-m-d H:i:s');
                
                if ($data[20] <> "") {
                    $inspection->rule_description = "Type A Citation: " . $data[20] . ";<br/> ";
                }

                if ($data[21] <> "") {
                    $inspection->rule_description = "Type B Citation: " . $data[21] . ";<br/> ";
                }

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