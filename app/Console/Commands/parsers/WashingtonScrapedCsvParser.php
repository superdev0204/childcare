<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class WashingtonScrapedCsvParser extends Command
{
    protected $signature = 'custom:parse-washington-scraped-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/washington-state/WashingtonChildCare.csv';
    protected $filename = [];

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

            $this->info("test $row = " . $data[0]);
            
            if ($data[0] <> '') {
                $facility = Facility::where('state_id', $data[0])
                                    ->where('state', 'WA')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[4])
                                    ->where('state', 'WA')
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[17], $data[18]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }
            
            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->approved = 1;
                $facility->filename = $uniqueFilename;
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[1] . " | " . $data[43]);

                if ($facility->approved == -5) {
                    continue;
                }
            }

            if ($data[43]<> "") {
                $facility->name = $data[43];
            } else {
                $facility->name = $data[1];
            }
            
            if($data[5] == "Family Child Care Home") {
                $contactName = explode(" ", $data[1], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }
            
            if ($data[16] <> "") {
                $facility->address = $data[16];
            }
            
            $facility->city = $data[17];
            $facility->state = $data[18];
            $facility->zip = substr($data[19],0,5);
            #$facility->county = $data[4];
            $facility->phone = $data[20];
            
            $city = Cities::where('state', $facility->state)
                        ->where('city', $facility->city)
                        ->first();
            
            if ($city) {
                $facility->cityfile = $city->filename;
                $facility->county = $city->county;
            }
            
            $data[6] = str_replace(" children", "", $data[6]);

            if ($data[6] <> "") {
                $facility->capacity = $data[6];
            }

            $facility->status = $data[2];
            $facility->state_id = $data[0];

            if ($data[4] <> "") {
                $facility->operation_id = $data[4];
            }

            $facility->age_range = $data[7];
            $facility->type = $data[5];

            if ($facility->type == "Family Child Care Home") {
                $facility->is_center = 0;
                if ($facility->capacity >= 10) {
                    $facility->approved = 2;
                }
            } else {
                $facility->is_center = 1;
            }

            if ($data[2] == "Open") {
                $facility->approved = 1;
            } else {
                $facility->approved = -1;
            } 
            
            if ($data[10] <> "" && preg_match("/Initial License Date/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Initial License Date: " . $data[10] . ".  ";
            }

            /* if ($data[21] <> "" && preg_match("/Doing Business As/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= " " . $data[21] . ".  ";
            } */
            if ($data[21] <> "") {
                $facility->website = $data[21];
            }
            if ($data[22] <> "" && $facility->email == '') {
                $facility->email = $data[22];
            }
            if ($data[24] <> "" && preg_match("/Level/i",$data[24])
                && preg_match("/Early Achievers/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Early Achievers Status: " . $data[24] . ".  ";
            }
            #if (preg_match("/Level/i",$data[24])) {
                $facility->state_rating = $data[24];
            #}
            if ($data[41] <> "") {
                $facility->language = $data[41];
            }
            if ($data[42] == 'Yes') {
                $facility->subsidized = 1;
            }
            if ($data[23] == 'Yes' || $data[25] == 'Yes' || $data[26] == 'Yes') {
                $facility->headstart = 1;
            }
            
            #$facility->district_office =  $data[48];
            $facility->do_phone =  $data[13];
            $facility->licensor =  $data[11];
            if ($data[15] <> "") {
                $facility->licensor =  $facility->licensor . " (" . $data[15] . ")";
            }

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
                $facilityDetail->initial_application_date = date('Y-m-d', strtotime($data[10]));
            }

            /* if ($data[14] <> "Non-Expiring" && $data[14] <> "") {
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[14]));
            } elseif ($facilityDetail->current_license_expiration_date <> "") {
                $facilityDetail->current_license_expiration_date = "2999-12-31";
            } */

            if ($data[36] <> "") {
                $facilityDetail->mailing_address = $data[36];
            }
            
            if ($data[37] <> "") {
                $facilityDetail->mailing_city = $data[37];
            }
            
            if ($data[38] <> "") {
                $facilityDetail->mailing_state = $data[38];
            }
            
            if ($data[39] <> "") {
                $facilityDetail->mailing_zip = $data[39];
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
            
            if ($data[29]) {
                $facilityHour->monday = $data[29];
            }
            
            if ($data[30]) {
                $facilityHour->tuesday = $data[30];
            }
            
            if ($data[31]) {
                $facilityHour->wednesday = $data[31];
            }
            
            if ($data[32]) {
                $facilityHour->thursday = $data[32];
            }
            
            if ($data[33]) {
                $facilityHour->friday = $data[33];
            }
            
            if ($data[34]) {
                $facilityHour->saturday = $data[34];
            }
            
            if ($data[35]) {
                $facilityHour->sunday = $data[35];
            }
            
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