<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class PennsylvaniaInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-pennsylvania-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/pennsylvania/PennsylvaniaInspections.csv';

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
                                ->where('state', 'PA')
                                ->first();

            if ($facility == null) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime($data[2]));

                $inspection = Inspections::where('facility_id', $facility->id)
                                        ->where('report_date', $reportDate)
                                        ->where('report_url', $data[3])
                                        ->where('report_status', $data[4])
                                        ->where('report_type', 'LIKE', $data[5] . '%')
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
                $inspection->report_url = $data[3];
                $inspection->report_status = $data[4];

                $inspection->rule_description = "<p><strong>Noncompliance Area:</strong> " . $data[6] . "</p>";
                $inspection->rule_description .= ' <p><strong>Correction Required:</strong> ' . $data[7] . '</p>';
                
                $inspection->current_status = $data[11];
                $inspection->status_date = date('Y-m-d', strtotime($data[9]));
                $inspection->provider_response = $data[8];
                $inspection->report_type = $data[5] . " - " . $data[10];
                $inspection->state = 'PA';

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