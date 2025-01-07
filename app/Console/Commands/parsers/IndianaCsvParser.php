<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;
use App\Models\Zipcodes;

class IndianaCsvParser extends Command
{
    protected $signature = 'custom:parse-indiana-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/indiana/IndianaChildCare.csv';
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
            $data[4] = 'IN';
            if ($data[6]=='') {
                $zip = Zipcodes::where('county', $data[5])
                                ->where('zipcode', $data[7])
                                ->first();
            
            	if ($zip) {
            		$data[6] = $zip->city;
            	}
            }
            
            $this->info("test $row = " . $data[1]);

            $facility = Facility::where('state_id', $data[0])
                                ->where('zip', $data[7])
                                ->where('state', $data[4])
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[18])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', str_replace(" ", "", $data[18]))
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[8])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[18])
                                    ->where('address', $data[8])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[18])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', 'LIKE', '%' . substr($data[18], -8))
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', 'LIKE', '%' . substr($data[18], -8))
                                    ->where('address', 'LIKE', '%' . substr($data[8], 0, 10))
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[4], 'in');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->county = $data[5];
                $facility->city = $data[6];
                
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
                $this->info("existing $existCount " . $facility->name . " | " . $data[1]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            if ($data[3]<>'') {
                $facility->state_rating = $data[3];
            }

            $facility->name = $data[1];
            $facility->address = $data[8];
            $facility->state = $data[4];
            $facility->zip = $data[7];
            $facility->phone = $data[18];
            $facility->state_id = $data[0];

            $contactName = explode(" ", $data[17], 2);
            $facility->contact_lastname = $contactName[1];
            $facility->contact_firstname = $contactName[0];

            if ($data[14]<> '') {
                $facility->capacity = $data[14];
            }

            if ($data[15] <> '') {
                $facility->accreditation = $data[15];
            }

            $facility->age_range = $data[13];
            $facility->hoursopen = $data[20];
            $facility->daysopen = 'Monday-Friday';

            if ($data[25]<>'') {
                $facility->daysopen .= ', Saturday';
            }

            if ($data[19]<>'') {
                $facility->daysopen .= ', Sunday';
            }

            $facility->type = $data[2];

            if (preg_match( "/Home/i",$data[2])) {
                $facility->is_center = 0;
                if ($facility->capacity >= 12 || $facility->type=="Licensed Home") {
                    $facility->approved = 2;
                }
            } else {
                $facility->is_center = 1;
            }

            if ($facility->approved <= 0) {
                $facility->approved = 1;
            }

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
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[11]));
            }

            if (isset($facilityDetail->id)) {
                $facilityDetail->save();
            } else {
                DB::table('facilitydetail')->insert((array) $facilityDetail);
            }

            if ($data[20] <> "" || $data[21]) {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }

                if ($data[20] <> "" ) {
                    $facilityHour->monday = $data[20];
                }

                if ($data[21] <> "" ) {
                    $facilityHour->tuesday = $data[21];
                }

                if ($data[22] <> "" ) {
                    $facilityHour->wednesday = $data[22];
                }

                if ($data[23] <> "" ) {
                    $facilityHour->thursday = $data[23];
                }

                if ($data[24] <> "" ) {
                    $facilityHour->friday = $data[24];
                }

                if ($data[25] <> "" ) {
                    $facilityHour->saturday = $data[25];
                }

                if ($data[19] <> "" ) {
                    $facilityHour->sunday = $data[19];
                }

                if (isset($facilityHour->id)) {
                    $facilityHour->save();
                } else {
                    DB::table('facilityhours')->insert((array) $facilityHour);
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