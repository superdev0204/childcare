<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Zipcodes;

class NewYorkCityCsvParser extends Command
{
    protected $signature = 'custom:parse-new-york-city-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/new-york/NYCHealth.csv';
    protected $filename = [];

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

            $data[1] = substr($data[1],0,75);
            $data[5] = substr($data[5],0,5);

            $zip = Zipcodes::where('state', 'NY')
                                ->where('zipcode', $data[5])
                                ->first();

            if ($zip) {
                $cityfile = $zip->cityfile;
                $cityname = $zip->city;
            } else {
                $cityname = $data[4];
            }

            $this->info("test $row = " . $data[0]);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'NY')
                                ->first();
            
            if (!$facility && $data[7] <> 'N/A') {
                $facility = Facility::where('operation_id', $data[7])
                                    ->where('address', $data[3])
                                    ->first();
            }

            if (!$facility && $data[6] <> '') {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[6])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility && $data[6] <> '') {
                $facility = Facility::where('phone', $data[6])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('address', $data[3])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $cityname, 'ny');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = $data[1];
                $facility->address = $data[3];
                $facility->city = $cityname;
                $facility->state = 'NY';
                $facility->zip = $data[5];
                $facility->county = $data[4];
                $facility->phone = $data[6];
                $facility->is_center = 1;
                $facility->type = 'Group Child Care Services';
                $facility->approved = 1;

                $facility->filename = $uniqueFilename;
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[1]);
            }

            if ($facility->state_id=="") {
                $facility->state_id = $data[0];
            }

            if ($facility->operation_id =="") {
                $facility->operation_id = $data[7];
            }

            $facility->status = $data[9];

            if ($facility->age_range=="") {
                $facility->age_range = $data[10];
            } elseif ($facility->ludate == $facility->created_date) {
                $facility->age_range = $facility->age_range . "; " . $data[10];
            }

            if ($data[11]<> "") {
                if ($facility->capacity==0) {
                    $facility->capacity = $data[11];
                } elseif ($facility->ludate == $facility->created_date) {
                    $facility->capacity = $facility->capacity + $data[11];
                }
            }

            if($data[13] == "Private" || $data[13]=='Corporate') {
                $facility->type = $data[14];
            } else {
                $facility->type = $data[13];
            }

            if ($data[12] == "Yes" && preg_match("/Certified to Administer Medication/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Certified to Administer Medication; ";
            }

            if ($data[15] <> "" && preg_match("/Years in Operation/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Years in Operation: " . $data[15];
            }

            if ($facility->district_office=="") {
                $facility->district_office = 'New York City Department of Health';
                $facility->do_phone = '311 or (212) NEW-YORK';
            }

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                DB::table('facility')->insert((array) $facility);
            }
        }

        $this->info("$existCount exists; $newCount new; $skipCount skipped");

        fclose($handle);
    }

    public function getUniqueFilename($name, $city, $state)
    {
        $name = str_replace(["`",":",".","'",",","#","\"","\\"],"", $name);
        $name = preg_replace('/[^a-zA-Z\d-]/', '-', $name);

        $city = str_replace([":",".","'",",","#","\""],"", $city);
        $city = preg_replace('/[^a-zA-Z\d-]/', '-', $city);

        $namecity = $name. "-" . $city;
        $namecity = strtolower($namecity);

        $filename = $this->filename;

        if (!isset($filename[$namecity])) {
            $filename[$namecity] = 1;
        } else {
            $filename[$namecity] = $filename[$namecity] + 1;
            $name = $name . $filename[$namecity];
        }

        $uniqueFilename = strtolower($name . "-" . $city . "-" . $state);
        $uniqueFilename = str_replace(["'","&","."],"",$uniqueFilename);
        $uniqueFilename = preg_replace('/[^a-zA-Z\d-]/', '-', $uniqueFilename);
        $uniqueFilename = preg_replace('/-{2,}/', '-', $uniqueFilename);

        $this->filename = $filename;

        return $uniqueFilename;
    }
}