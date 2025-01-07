<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class NorthCarolinaCsvParser extends Command
{
    protected $signature = 'custom:parse-north-carolina-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/north-carolina/NorthCarolinaChildCareNew.csv';
    protected $filename = [];

    public function handle()
    {
        $existCount = 0;
        $newCount = 0;
        $skipCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 2000, ";")) !== false) {
            $row++;

            if ($row <= 1) {
                continue;
            }

            if ($data[1] == "Address not published" && $data[4]== "") {
                $skipCount++;
                continue;
            }
            
            $this->info("test $row = " . $data[0]);
            
            if ($data[10] == "Family Child Care Home") {
                $address = explode(" ", $data[1], 2);
                if ($address[1] != "") {
                    $data[1] = $address[1];
                }
            }

            $facility = Facility::where('state_id', trim($data[7]))
                                ->where('state', 'NC')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('operation_id', trim($data[7]))
                                    ->where('state', 'NC')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[1])
                                    ->where('zip', $data[4])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[5])
                                    ->where('address', $data[1])
                                    ->where('zip', $data[4])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[0]))
                                    ->where('phone', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[2], $data[3]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = $data[0];
                $facility->address = $data[1];
                $facility->city = $data[2];
                $facility->state = $data[3];
                $facility->zip = $data[4];
                $facility->phone = $data[5];
                $facility->county = $data[12];

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }

                $facility->filename = $uniqueFilename;
                $facility->approved = 1;
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[0]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->state_id = $data[7];
            $facility->operation_id = $data[7];

            if (preg_match( "/Star/i",$data[6])) {
                $facility->type = $data[6];
            } else {
                $facility->type = $data[10];
            }

            $facility->subsidized = ($data[11] == "Yes") ? 1 : 0;

            if (preg_match( "/One Star/i",$data[6])) {
                $facility->state_rating = 1;
            }

            if (preg_match( "/Two Star/i",$data[6])) {
                $facility->state_rating = 2;
            }

            if (preg_match( "/Three Star/i",$data[6])) {
                $facility->state_rating = 3;
            }

            if (preg_match( "/Four Star/i",$data[6])) {
                $facility->state_rating = 4;
            }

            if (preg_match( "/Five Star/i",$data[6])) {
                $facility->state_rating = 5;
            }

            if(preg_match( "/Not Available/i",$data[8]) == false && $data[8] <> "" && $facility->user_id == 0) {
                $facility->email = $data[8];
            }

            if(preg_match( "/Not Available/i",$data[9]) == false && $data[9] <> "" && $facility->website == "") {
                $facility->website = $data[9];
            }
            $facility->age_range = $data[18];
            $facility->capacity = $data[23];
            
            if ($data[10] == "Family Child Care Home") {
                $facility->is_center = 0;
                if ($facility->state_rating >=4 || $facility->subsidized) {
                    $facility->approved = 2;
                }
            } else {
                $facility->is_center = 1;
            }

            $facility->district_office = "North Carolina Dept of Health and Human Services - Division of Child Development";
            $facility->do_phone = "(919) 662-4499";

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }
            
            if ($data[17] <> "") {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();
                
                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[17]));
                if ($data[13] <> "") {
                    $facilityDetail->fax = $data[13];
                } else {
                    $facilityDetail->fax = $data[44];
                }
                $facilityDetail->phone2 = $data[43];
                $facilityDetail->license_holder = $data[38];
                $facilityDetail->mailing_address = $data[39];
                $facilityDetail->mailing_city = $data[40];
                $facilityDetail->mailing_state = $data[41];
                $facilityDetail->mailing_zip = $data[42];
                
                if (isset($facilityDetail->id)) {
                    $facilityDetail->save();
                } else {
                    DB::table('facilitydetail')->insert((array) $facilityDetail);
                }
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