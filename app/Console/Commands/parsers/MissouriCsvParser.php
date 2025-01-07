<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class MissouriCsvParser extends Command
{
    protected $signature = 'custom:parse-missouri-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/missouri/MissouriChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[0]);
            
            $data[3] = substr($data[3],0,5);
            $data[4] = trim($data[4]);
            $data[12] = trim($data[12]);
            $data[13] = str_replace("https://healthapps.dhss.mo.gov/childcaresearch/Facility.aspx?LID=","",$data[13]);

            if ($data[7] == "FAMILY HOME" || $data[7] == "Registered Family Home") {
                $address = explode(" ", $data[4], 2);
                $data[4] = $address[1];
            }

            $facility = Facility::where('state_id', $data[21])
                                ->where('state', 'MO')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('operation_id', $data[13])
                                    ->where('state', 'MO')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[4])
                                    ->where('city', $data[1])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[3])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[5])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[3])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[5])
                                    ->where('name', $data[0])
                                    ->where('zip', $data[3])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[5])
                                    ->where('zip', $data[4])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[1], 'mo');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->state = "MO";
                $facility->filename = $uniqueFilename;
                $facility->created_date = date('Y-m-d H:i:s');
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

            $facility->name = $data[0];
            $facility->city = $data[1];
            $facility->zip = $data[3];
            $facility->county = $data[2];
            
            if ($facility->user_id == "0" || $facility->user_id == null) {
                if ($facility->address <> $data[4]) {
                    $facility->address = $data[4];
                }
                if ($facility->phone <> $data[5]) {
                    $facility->phone = $data[5];
                }
            }

            $city = Cities::where('state', $facility->state)
                        ->where('city', $facility->city)
                        ->first();
            
            if ($city) {
                $facility->cityfile = $city->filename;
                $facility->county = $city->county;
            }
            
            $facility->state_id = $data[21];
            $facility->operation_id = $data[13];
            $facility->type = $data[7];

            if ($facility->email == "" && $data[18] <> "") {
                $facility->email = $data[18];
            }

            if ($data[10] <> '') {
                $facility->capacity = $data[10];
            }

            if ($data[11] <> '') {
                $facility->age_range = $data[11];
            }

            if ($facility->user_id == 0 && $facility->daysopen=="") {
                $facility->daysopen = "Monday - Friday";
            }

            if ($data[12] <>"0" && $data[12] <> "") {
                $facility->hoursopen = $data[12];
            }

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }
            if ($facility->type == "FAMILY HOME" || $facility->type == "GROUP HOME" || $facility->type == 'Registered Family Home') {
                $facility->is_center =  0;
                if ($facility->type == "GROUP HOME") {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            if($data[19] <> "") {
                $contactName = explode(", ", $data[19], 2);
                $facility->contact_lastname = $contactName[0];
                $facility->contact_firstname = $contactName[1];
            }

            #$facility->district_office = 'Missouri Dept of Health and Senior Services - Section for Child Care Regulation';
            $facility->licensor = $data[20];

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

            if ($data[8] <> "") {
                $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[8]));
            }

            if ($data[9] <> "") {
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[9]));
            }

            if ($data[14] <> "") {
                $facilityDetail->mailing_address = $data[14];
            }

            if ($data[15] <> "") {
                $facilityDetail->mailing_city = $data[15];
            }

            if ($data[16] <> "") {
                $facilityDetail->mailing_state = $data[16];
            }

            if ($data[17] <> "") {
                $facilityDetail->mailing_zip = $data[17];
            }

            if (isset($facilityDetail->id)) {
                $facilityDetail->save();
            } else {
                DB::table('facilitydetail')->insert((array) $facilityDetail);
            }

            $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();
            
            if (!$facilityHour) {
                $facilityHour = new \stdClass();
                $facilityHour->facility_id = $facility->id;
            }

            $facilityHour->monday = $data[12];
            $facilityHour->tuesday = $data[12];
            $facilityHour->wednesday = $data[12];
            $facilityHour->thursday = $data[12];
            $facilityHour->friday = $data[12];

            if (isset($facilityHour->id)) {
                $facilityHour->save();
            } else {
                DB::table('facilityhours')->insert((array) $facilityHour);
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