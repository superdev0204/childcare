<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Inspections;

class AlabamaInspectionsCsvParser extends Command
{
    protected $signature = 'custom:parse-alabama-inspections-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/alabama/AlabamaSubstComplaints.csv';    

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        if ($handle === false) {
            $this->error("Unable to open the data file '{$filename}'");
            return;
        }

        while (($data = fgetcsv($handle, 2000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $facility = Facility::where('state_id', trim($data[0]))
                                ->where('state', 'AL')
                                ->first();
            
            if (!$facility) {
                $skipCount++;
                $this->info("skip record $row " . $data[0]);
            } else {
                
                $enddate = date('Y-m-d', strtotime(str_replace('-', '/', $data[3])));
                
                if (preg_match("/AlabamaAdverseActions/i",$this->file)) {
                    $startdate = date('Y-m-d', strtotime(str_replace('-', '/', $data[4])));
                }

                $query = Inspections::where('facility_id', $facility->id)
                                    ->where('report_type', $data[1])
                                    ->where('report_date', $enddate);
                
                if (preg_match("/AlabamaEvalDefic/i",$this->file)) {
                    $query->where('report_url', $data[2]);
                } else {
                    $query->where('report_status', $data[2]);
                }
                    
                $inspection = $query->first();

                if (!$inspection) {
                    $newCount++;

                    $this->info("new record $newCount");

                    $inspection = new \stdClass();
                    $inspection->facility_id = $facility->id;
                    $inspection->inserted = date('Y-m-d H:i:s');
                } else {
                    $this->info("exist record $row " . $data[0]);
                    $existCount++;
                }
                
                $inspection->report_type = $data[1];
                $inspection->report_date = $enddate;
                if (preg_match("/AlabamaAdverseActions/i",$this->file)) {
                    $inspection->status_date = $startdate;
                }
                if (preg_match("/AlabamaEvalDefic/i",$this->file)) {
                    $inspection->report_url = $data[2];
                } else {
                    $inspection->report_status = $data[2];
                }                
                
                $inspection->updated = date('Y-m-d H:i:s');
                $inspection->state = 'AL';
                if (isset($inspection->id)) {
                    $inspection->save();
                } else {
                    DB::table('inspections')->insert((array) $inspection);
                }
            }
        }

        $this->info("$existCount exists; $newCount new; $skipCount skipped");

        fclose($this->getHandle());
    }
}