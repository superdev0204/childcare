<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class NewMexicoCsvParser extends Command
{
    protected $signature = 'custom:parse-new-mexico-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/new-mexico/NewMexicoChildCare.csv';
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

            if (strlen($data[5]) > 5) {
                $data[8] = substr($data[5],0,5);
            }

            $data[15] = preg_replace("/English; /", "", $data[15]);
            $data[15] = preg_replace("/English, /", "", $data[15]);
            $data[15] = preg_replace("/English/", "", $data[15]);

            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[6]);

            if(strlen($strippedPhoneNumber) == 10) {
                $data[6] = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $strippedPhoneNumber);
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'NM')
                                ->first();
            
            if (!$facility && $data[16] <> '') {      
                $facility = Facility::where('operation_id', $data[16])
                                    ->where('state', 'NM')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[6])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[6])
                                    ->where('city', $data[3])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[3], 'nm');

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
                $facility->county = $data[7];
                $facility->phone = $data[6];

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
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }
            $facility->name = $data[1];
            $facility->address = $data[2];
            $facility->state_id = $data[0];
            $facility->operation_id = $data[16];
            $facility->type = $data[17];
            $facility->headstart = ($data[17] == "(CCC) Head Start") ? 1 : 0;
            $facility->email = $data[8];
            $facility->website = $data[9];

            $facility->age_range = $data[11];
            $facility->typeofcare = $data[12];
            $facility->language = $data[15];

            if ($data[19] <> '') {
                $facility->subsidized = 1;
            }

            $facility->transportation = $data[21];
            $facility->schools_served = $data[22];

            if ($data[30] <> "") {
                $facility->lat = $data[30];
            }
            if ($data[31] <> "") {
                $facility->lng = $data[31];
            }
            
            if (trim($data[10]) <> "" && preg_match("/Quality Rating/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Quality Rating: " . $data[10] . "; ";
                if (preg_match("/-star/i",$data[10])) {
                    $star = explode("-", $data[10], 2);
                    $facility->state_rating = $star[0];
                }
            }

            if ($data[13] <> "" && preg_match("/Environment/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Environment: " . $data[13] . "; ";
            }

            if ($data[14] <> "" && preg_match("/Special Needs/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Special Needs: " . $data[14] . "; ";
            }

            if ($facility->approved <= 0) {
                $facility->approved = 1;
            }

            if (preg_match("/family/i",$data[17])) {
                $facility->is_center =  0;
                $facility->approved = 2;
            } else  {
                $facility->is_center =  1;
            }

            if ($facility->user_id == 0 && $data[23]<>"") {
                $facility->daysopen = "Monday - Friday";
                $facility->hoursopen = $data[23];
                if ($data[28] <> "") {
                    $facility->daysopen .= ", Saturday";
                }
                if ($data[29] <> "") {
                    $facility->daysopen .= ", Sunday";
                }
            }

            if (preg_match( "/Cibola|McKinley|San Juan|San Miguel|Mora|Colfax|Harding|Union|Santa Fe|Taos|Rio Arriba|Los Alamos/i",$facility->county)) {
                $facility->district_office = 'Child Care Licensing - Northern Region';
                $facility->do_phone = '(505) 476‐5440';
            } elseif (preg_match( "/Bernalillo|Torrance|Sandoval|Valencia|Socorro/i",$facility->county)) {
                $facility->district_office = 'Child Care Licensing - Central Region';
                $facility->do_phone = '(505) 841‐4825';
            } elseif (preg_match( "/Quay|Guadalupe|Curry|De Baca|Roosevelt|Chaves|Lea|Eddy/i",$facility->county)) {
                $facility->district_office = 'Child Care Licensing - Southeast Region';
                $facility->do_phone = '(575) 625-1078';
            } elseif (preg_match( "/Dona Ana|Grant|Hidalgo|Catron|Luna|Sierra|Otero|Lincoln/i",$facility->county)) {
                $facility->district_office = 'Child Care Licensing - Southwest Region';
                $facility->do_phone = '(575) 373-6609';
            } else {
                $facility->district_office = 'New Mexico Children, Youth and Families Department';
                $facility->do_phone = '1-800-832-1321';
            }

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[23] <> "" || $data[24] <> "" ) {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }

                if ($data[23] <> "" ) {
                    $facilityHour->monday = $data[23];
                }

                if ($data[24] <> "" ) {
                    $facilityHour->tuesday = $data[24];
                }

                if ($data[25] <> "" ) {
                    $facilityHour->wednesday = $data[25];
                }

                if ($data[26] <> "" ) {
                    $facilityHour->thursday = $data[26];
                }

                if ($data[27] <> "" ) {
                    $facilityHour->friday = $data[27];
                }

                if ($data[28] <> "" ) {
                    $facilityHour->saturday = $data[28];
                }

                if ($data[29] <> "" ) {
                    $facilityHour->sunday = $data[29];
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