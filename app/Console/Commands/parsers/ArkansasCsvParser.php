<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class ArkansasCsvParser extends Command
{
    protected $signature = 'custom:parse-arkansas-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/arkansas/ArkansasChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $existCount = 0;
        $newCount = 0;
        $skipCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 3000, ";")) !== false) {
            $row++;

            if ($row <= 1) {
                continue;
            }

            $this->info("test $row = " . $data[0]);

            $data[2] = trim($data[2]);

            if ($data[8] == "Registered Child Care Family Home") {
                $address = explode(" ", $data[2], 2);
                if ($address[1] != "") {
                    $data[2] = $address[1];
                }
            }

            $facility = Facility::where('state_id', trim($data[0]))
                                ->where('state', 'AR')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', trim($data[0]))
                                    ->where('state', 'AZ')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[1]))
                                    ->where('phone', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[7])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[3], $data[4]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[1];
                $facility->address = $data[2];
                $facility->city = $data[3];
                $facility->state = $data[4];
                $facility->zip = $data[5];
                $facility->county = $data[6];
                $facility->phone = $data[7];

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }

                $facility->approved = 1;
                $facility->filename = $uniqueFilename;
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[1]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }

            $facility->state_id = $data[0];
            $facility->operation_id = $data[0];
            $facility->type = $data[8];

            if ($data[8] == "Registered Child Care Family Home" || $data[8] == "Licensed Child Care Family Home") {
                $facility->is_center = 0;
            } else {
                $facility->is_center = 1;
            }

            if ($data[8] == "Licensed Child Care Family Home") {
                $facility->approved = 2;
            }

            if ($facility->website == "") {
                $facility->website = $data[9];
            }

            if($data[10] != "") {
                $contactName = explode(" ", $data[10], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }

            $facility->subsidized = ($data[11] == "Yes") ? 1 : 0;

            if ($data[12] == "Yes" && preg_match("/ABC Facility/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "ABC Facility; ";
            }

            if ($data[13] == "Yes" && preg_match("/Headstart Facility/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Headstart Facility; ";
            }

            if ($data[14] <> "") {
                $facility->state_rating = $data[14];
                if (preg_match("/Better Beginnings/i",$facility->additionalInfo) == false) {
                    $facility->additionalInfo .= $data[14] . " Stars Better Beginnings; ";
                }
            }

            $facility->licensor = $data[15] . " (" . $data[20] . ")";
            $facility->district_office =  trim($data[16]) . ", " . $data[17] . " " . $data[18];
            $facility->do_phone = $data[19];

            $facility->pricing = trim($data[29]);

            $capacity = 0;
            $agerange = "";

            if ($data[30] > 0) {
                $capacity += $data[30];
                $agerange .= "Infant/Toddler; ";
            }

            if ($data[31] > 0) {
                $capacity += $data[31];
                $agerange .= "Preschool; ";
            }

            if ($data[32] > 0) {
                $capacity += $data[32];
                $agerange .= "School Age; ";
            }

            if ($data[33] > 0) {
                $capacity += $data[33];
                $facility->typeofcare = $agerange . "Sick Care; ";
            }

            if ($capacity > 0) {
                $facility->capacity = $capacity;
                $facility->age_range = $agerange;
            }

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

            $facilityHour->monday = $data[22];
            $facilityHour->tuesday = $data[23];
            $facilityHour->wednesday = $data[24];
            $facilityHour->thursday = $data[25];
            $facilityHour->friday = $data[26];
            $facilityHour->saturday = $data[27];
            $facilityHour->sunday = $data[28];

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