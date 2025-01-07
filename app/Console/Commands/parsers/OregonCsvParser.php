<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class OregonCsvParser extends Command
{
    protected $signature = 'custom:parse-oregon-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/oregon/ChildCareOregon.csv';
    protected $filename = [];

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
                $this->info("skip " . ++$skipCount);
                continue;
            }

            $this->info("test $row = " . $data[1]);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'OR')
                                ->first();
            
            if (!$facility && $data[4]<> "") {
                $facility = Facility::where('operation_id', $data[4])
                                    ->where('state', 'OR')
                                    ->first();
            }
            
            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[12]);
            
            if (!$facility && $data[6] <> "") {
                $address = str_replace(['street', 'avenue', 'drive', 'road', '.'], ['st', 'ave', 'dr', 'rd', ''], strtolower($data[6]));
                $facility = Facility::where('name', $data[1])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", ["%" . str_replace(['avenue', 'street', 'drive', 'road', '.'], ['ave', 'st', 'dr', 'rd', ''], strtolower($address)) . "%"])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility && $data[12] <> "") {
                $facility = Facility::where('name', $data[1])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '(', ''), ')', ''), '-', '') LIKE ?", [$strippedPhoneNumber])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility && $data[12] <> "" && $data[5]<>"") {
                $facility = Facility::where('name', $data[5])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '(', ''), ')', ''), '-', '') LIKE ?", [$strippedPhoneNumber])
                                    ->where('zip', $data[9])
                                    ->first();
            }
            
            if (!$facility && $data[12] <> "") {
                $facility = Facility::where('name', $data[1])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '(', ''), ')', ''), '-', '') LIKE ?", [$strippedPhoneNumber])
                                    ->where('city', $data[7])
                                    ->first();
            }

            if (!$facility && $data[12] <> "" && $data[5] <> '') {
                $facility = Facility::where('name', $data[5])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '(', ''), ')', ''), '-', '') LIKE ?", [$strippedPhoneNumber])
                                    ->where('city', $data[7])
                                    ->first();
            }
            
            if (!$facility && $data[12] <> "") {
                $facility = Facility::where('name', $data[1])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '(', ''), ')', ''), '-', '') LIKE ?", [$strippedPhoneNumber])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[7], 'or');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");
                $facility = new \stdClass();
                $facility->state = "OR";
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

            $facility->state_id = $data[0];
            $facility->operation_id = $data[4];
            
            $facility->name = $data[1];
            if ($data[6] <> "") {
                $facility->address = $data[6];
            }
            $facility->city = $data[7];
            $facility->zip = $data[9];
            if ($data[12] <> "") {
                $facility->phone = $data[12];
            }
            
            if ($data[10]<>"") {
                $facility->lng = $data[10];
            }
            
            if ($data[11]<> "") {
                $facility->lat = $data[11];
            }
            
            $city = Cities::where('state', $facility->state)
                        ->where('city', $facility->city)
                        ->first();
            
            if ($city) {
                $facility->cityfile = $city->filename;
                $facility->county = $city->county;
            }
            if ($data[22] <> "") {
                $facility->type = $data[22];
            } else {
                $facility->type = $data[3];
            }
            if ($data[13] <> "") {
                $facility->website = $data[13];
            }
            $facility->age_range = $data[14];
            if ($data[23] <> '') {
                $facility->capacity = $data[23];
            }
            if ($data[28 <> ""]) {
                $facility->language = $data[28];
            }
            
            if ($data[40]<> "") {
                $facility->transportation = $data[40];
            }
            if (preg_match("/NOT willing to accept DHS/", $data[41]) == false && preg_match("/willing to accept DHS/i", $data[41])) {
                $facility->subsidized = 1;
            }
            if ($data[42] <> "") {
                $facility->state_rating = $data[42];
            }
            if ($data[34] <> "" && $facility->typeofcare == "") {
                $facility->typeofcare = $data[34];
            }
            if ($data[36] <> "" && preg_match("/Extended Hour Care/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Extended Hour Care: " . $data[36];
            }

            if (preg_match("/Family Child Care/i",$data[22])) {
                $facility->is_center =  0;
                if ($data[22] == "Certified Family Child Care") {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            $facility->contact_firstname = $data[5];
            $facility->district_office = 'Oregon Employment Department - Child Care Division';
            $facility->do_phone = '503-947-1400';

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

            if (!$facilityHour) {
                $facilityHour = new \stdClass();
                $facilityHour->facility_id = $facility->id;
            }

            if ($data[15]) {
                $facilityHour->monday = $data[15];
            }

            if ($data[16]) {
                $facilityHour->tuesday = $data[16];
            }

            if ($data[17]) {
                $facilityHour->wednesday = $data[17];
            }

            if ($data[18]) {
                $facilityHour->thursday = $data[18];
            }

            if ($data[19]) {
                $facilityHour->friday = $data[19];
            }

            if ($data[20]) {
                $facilityHour->saturday = $data[20];
            }

            if ($data[21]) {
                $facilityHour->sunday = $data[21];
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