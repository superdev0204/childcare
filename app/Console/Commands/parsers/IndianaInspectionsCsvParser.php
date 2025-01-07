<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class IndianaInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-indiana-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/indiana/IndianaInspections.csv';

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

            if ($data[3]=="" && $data[4]=="" && $data[5]=="") {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);

                continue;
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'IN')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime($data[2]));

                $query = Inspections::where('facility_id', $facility->id)
                                    ->where('report_date', $reportDate)
                                    ->where('report_type', $data[1]);
                
                if ($data[4]<>'') {
                	$query->where('report_status', $data[4]);
                } else {
                    $query->where('report_status', null);
                }
                
                $inspection = $query->first();

                if (!$inspection) {
                    $newCount++;
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $this->info("exist record $row " . $data[0] . " - " . $data[1]);
                    $existCount++;
                }

                $inspection->report_date = $reportDate;
                $inspection->report_type = $data[1];

                if ($data[4] <> '') {
                    $inspection->report_status = $data[4];
                }
				if ($data[3] <> '') {
	                $inspection->rule_description = $data[3];
				} else {
					$inspection->rule_description = 'No NonCompliances Cited';
				}

                if ($data[5] <> '') {
                    $inspection->current_status = $data[5];
                }

                if ($data[6] <> '') {
                    $inspection->status_date = date('Y-m-d', strtotime($data[6]));
                }

                if ($data[7] <> '') {
                    $inspection->provider_response = $data[7];
                }

                if ($data[8] <> '') {
                    $inspection->report_url = $data[8];
                } 
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->state = 'IN';

                if (isset($inspection->id)) {
                    $inspection->save();
                } else {
                    DB::table('inspections')->insert((array) $inspection);
                }
            }
        }

        $this->logger->info("$existCount exists; $newCount new; $skipCount skipped");

        fclose($handle);
    }
}