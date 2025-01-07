<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class VermontCsvParser extends Command
{
    protected $signature = 'custom:parse-vermont-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/vermont/VermontChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $newCount = 0;
        $existCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 10000, ";")) !== false) {
            $row++;

            if ($row <= 1) {
                continue;
            }

            if ($data[12] == "Registered" || $data[12] == "Family Child Care") {
                $address = explode(" ", $data[4], 2);
                
                if ($address[1] != "") {
                    $data[4] = $address[1];
                }
            }
            $data[7] = str_replace("'", '', $data[7]);
            $data[7] = str_pad($data[7], 5, "0", STR_PAD_LEFT);
            
            if (strlen($data[8]) == 8) {
                $data[8] = "(802)" . $data[8];
            }
            
            $this->info("test $row = " . $data[0]);

            $facility = Facility::where('state_id', $data[41])
                                ->where('state', 'VT')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('operation_id', $data[41])
                                    ->where('state', 'VT')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }
            if (!$facility) {
                $facility = Facility::where('phone', $data[8])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) 		{
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[2], $data[6]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info('new record ' . $newCount);

                $facility = new \stdClass();
                $facility->name = $data[0];
                $facility->address = $data[4];
                $facility->city = $data[2];
                $facility->county = $data[5];
                $facility->state = $data[6];
                $facility->zip = $data[7];
                $facility->phone = $data[8];

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

            if (strlen($facility->phone) == 8) {
                $facility->phone = $data[8];
            }

            if ($data[12] == "Family Child Care") {
                $facility->approved = 2;
            }

            if ($data[12] == "Registered" || $data[12] == "Family Child Care") {
                $facility->is_center = 0;
            } else  {
                $facility->is_center = 1;
            }

            if ($data[3] == "Licensed Provider") {
                $facility->type = $data[12] . " - " . $data[3];
            } else {
                $facility->type = $data[3];
            }

            $facility->state_id = $data[41];
            $facility->operation_id = $data[41];

            if ($facility->email == "" && $data[9] <> "") {
                $facility->email = $data[9];
            }

            if ($facility->website == "" && $data[10] <> "") {
                $facility->website = $data[10];
            }

            if($data[13] != "") {
                $contactName = explode(" ", $data[13], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            } else if($data[14] != "") {
                $contactName = explode(" ", $data[14], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }

            if($data[22] != "") {
                $facility->age_range = $data[22];
            }

            if ($data[19]> 0) {
                $facility->capacity = $data[19];
            } else if ($data[12] == "Registered") {
                $facility->capacity = 6;
            }

            if ($data[15]> 0) {
                $facility->is_infant = 1;
            }

            if ($data[16]> 0) {
                $facility->is_toddler = 1;
            }

            if ($data[17]> 0) {
                $facility->is_preschool = 1;
            }

            if ($data[18]> 0) {
                $facility->is_afterschool = 1;
            }

            $facility->is_religious = ($data[24] == "Yes") ? 1 : 0;
            if ($data[25] == "Yes") {
                $facility->subsidized = 1;
            }

            if ($data[31] <> "") {
                $facility->typeofcare = $data[31];
            }
            if ($data[32] <> "" && $data[32] <> "English") {
                $facility->language = str_replace("English, ","",$data[32]);
                $facility->language = str_replace(", English","",$facility->language);
            }

            if ($data[34] <> "") {
                $facility->hoursopen = $data[34];
            }

            if ($data[35] <> "") {
                $facility->daysopen = str_replace(", Tuesday, Wednesday, Thursday,"," - ", $data[35]);
            }

            $facility->transportation = (trim($data[36]) != "") ? $data[36] : "";
            $facility->schools_served = trim($data[37]);

            if ($data[40] <> "") {
                $facility->accreditation = $data[40];
            }

            if ($facility->user_id == 0) {
                if ($data[21] <> "") {
                    $facility->introduction = $data[21];
                } else {
                    $facility->introduction = $data[20];
                }
            }

            if ($data[11] <> "" && preg_match("/Initial License Date/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Initial License Date: " . $data[11] . "; ";
            }

            if ($data[27] <> "Yes" && preg_match("/Sibling Discount Available/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Sibling Discount Available; ";
            }

            if ($data[28] <> "" && preg_match("/Area Description/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Area Description: " . trim($data[28]) . "; ";
            }

            if ($data[30] <> "" && preg_match("/Pets/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Pets: " . trim($data[30]) . "; ";
            }

            if ($data[33] <> "" && preg_match("/Special Schedule/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Special Schedule: " . trim($data[33]) . "; ";
            }

            if ($data[38] <> "" && preg_match("/Program Meals/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Program Meals: " . trim($data[38]) . "; ";
            }

            if (preg_match("/Star/i",$data[39])) {
                if ($data[39] <> "" && preg_match("/Star/i",$facility->additionalInfo) == false) {
                    $facility->additionalInfo .= $data[39] . " " . $facility->type . "; ";
                }
                $facility->state_rating = str_replace(" Star","",$data[39]);
            }

            $facility->district_office = 'Vermont Child Care Consumer Line';
            $facility->do_phone = '1-800-649-2642';

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

            if ($data[11] <> "") {
                $facilityDetail->initial_application_date = date('Y-m-d', strtotime($data[11]));
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