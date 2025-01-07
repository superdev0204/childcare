<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class WestVirginiaInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-west-virginia-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/west-virginia/InspectionsWestVirginia.csv';

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
                                ->where('state', 'WV')
                                ->first();

            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                $reportDate = date('Y-m-d', strtotime($data[1]));

                $query = Inspections::where('facility_id', $facility->id)
                                    ->where('report_date', $reportDate);

                if ($data[2] !== '') {
                    $complaintDate = date('Y-m-d', strtotime($data[2]));
                    $query->where('complaint_date', $complaintDate);
                } else {
                    $query->whereNull('complaint_date');
                }

                if ($data[5] !== '') {
                    $statusDate = date('Y-m-d', strtotime($data[5]));
                    $query->where('status_date', $statusDate);
                } else {
                    $query->whereNull('status_date');
                }

                if ($data[3] !== '') {
                    $query->where('rule_description', $data[3]);
                } else {
                    $query->whereNull('rule_description');
                }

                if ($data[4] !== '') {
                    $query->where('report_status', $data[4]);
                } else {
                    $query->whereNull('report_status');
                }

                $inspection = $query->first();

                if (!$inspection) {
                    $newCount++;
                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $this->info("exist record $row " . $data[0] . " - " . $data[3]);
                    $existCount++;
                }

                $inspection->report_date = $reportDate;

                if ($complaintDate <> '') {
                    $inspection->complaint_date = $complaintDate;
                }

                if ($statusDate <> '') {
                    $inspection->status_date = $statusDate;
                }

                if ($data[3] <> '') {
                    $inspection->rule_description = $data[3];
                }

                if ($data[4] <> '') {
                    $inspection->report_status = $data[4];
                }

                $inspection->state = 'WV';
                $inspection->updated = date('Y-m-d H:i:s');
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