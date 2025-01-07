<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class MontanaInspectionsParser extends Command
{
    protected $signature = 'custom:parse-montana-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/montana/MontanaInspections.csv';

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
                                ->where('state', 'MT')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime($data[1]));

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
                    $this->info("exist record $row " . $data[0] . " - " . $data[4]);
                    $existCount++;
                }

                $inspection->report_date = $reportDate;
                $inspection->report_type = $data[2];
                $inspection->report_url = $data[20];
                
                $inspection->current_status = $data[3];
                if ($data[4] <> '') {
                    $inspection->current_status .= " - " . $data[4];
                }
                $inspection->report_status = $data[5];
                $inspection->pages = $data[7];
                
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->state = 'MT';

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