<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;
use App\Models\Zipcodes;

class IndianaHomeCsvParser extends Command
{
    protected $signature = 'custom:parse-indiana-home-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/indiana/Indiana-homes.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 1000, "~")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            if ($data[1] == null) {
                $data[1] = $data[2];
            }
            
            $this->info("test $row = " . $data[0]);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'IN')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[0])
                                    ->where('state', 'IN')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[6])
                                    ->where('zip', $data[3])
                                    ->first();
            }

            $cityname = $data[3];
            $cityfile = "";

            $zip = Zipcodes::where('state', 'IN')
                            ->where('zipcode', $data[3])
                            ->first();

            if ($zip) {
                $cityfile = $zip->cityfile;
                $cityname = $zip->city;
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $cityname, 'in');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[1];
                $facility->city = $cityname;
                $facility->state = "IN";
                $facility->zip = $data[3];
                $facility->phone = $data[6];
                $facility->county = $data[5];

                $contactName = explode(" ", $data[2], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];

                $facility->cityfile = $cityfile;

                $facility->approved = 1;

                $facility->filename = $uniqueFilename;
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[1]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->state_id = $data[0];
            $facility->operation_id = $data[0];

            $facility->capacity = $data[7];
            if ($facility->capacity > 12) {
                $facility->approved = 2;
            }

            $facility->age_range = ($data[8]) . " to " . ($data[9]) . ' years old';
            $facility->type = "Child Care Home";
            $facility->is_center = 0;


            $facility->district_office = "Indiana Family and Social Services Administration - Bureau of Child Care";
            $facility->do_phone = "1-877-511-1144";

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

            if (!$facilityDetail) {
                $facilityDetail = new \stdClass();
                $facilityDetail->facility_id = $facility->id;
            }

            if ($data[10] <> "") {
                $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[10]));
            }

            if ($data[11] <> "") {
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[11]));
            }

            if (isset($facilityDetail->id)) {
                $facilityDetail->save();
            } else {
                DB::table('facilitydetail')->insert((array) $facilityDetail);
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