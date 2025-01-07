<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class ColoradoInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-colorado-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/colorado/ColoradoInspections.csv';

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
                                ->where('state', 'CO')
                                ->first();

            if (!$facility) {

                $skipCount++;
                $this->info("skip record $row " . $data[0]);

            } else {
                $reportDate = date('Y-m-d', strtotime($data[1]));

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_type', 'Inspection')
                                        ->where('report_date', $reportDate)
                                        ->where('current_status', $data[2])
                                        ->first();

                if (!$inspection) {
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->inserted = date('Y-m-d H:i:s');
                    $newCount++;
                } else {
                    $this->logger->info("exist record $row " . $data[0] . " - " . $data[2]);
                    $existCount++;
                }

                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->report_date = $reportDate;
                $inspection->report_type = 'Inspection';
                $inspection->state = 'CO';
                $inspection->report_status = $data[2];
                $inspection->report_url = $data[3];
                
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