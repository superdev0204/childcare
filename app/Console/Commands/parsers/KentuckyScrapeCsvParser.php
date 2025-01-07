<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class KentuckyScrapeCsvParser extends Command
{
    protected $signature = 'custom:parse-kentucky-scrape-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/kentucky/KentuckyChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 2000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[1]);

            if ($data[8] == 'CERTIFIED') {
            	$streetaddress = explode(" ", $data[2], 2);
            	if ($streetaddress[1] != "") {
            		$data[2] = $streetaddress[1];
            	}
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', $data[5])
                                ->first();

            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[7]);

            if (!$facility) {
                $facility = Facility::where('operation_id', $data[51])
                                    ->where('state', $data[5])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->first();
            }

            $address = str_replace(['street', 'avenue', 'drive', 'road', '.'], ['st', 'ave', 'dr', 'rd', ''], strtolower($data[2]));

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", ["%" . str_replace(['avenue', 'street', 'drive', 'road', '.'], ['ave', 'st', 'dr', 'rd', ''], strtolower($address)) . "%"])
                                    ->where('city', $data[3])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", ["%" . str_replace(['avenue', 'street', 'drive', 'road', '.'], ['ave', 'st', 'dr', 'rd', ''], strtolower($address)) . "%"])
                                    ->where('city', $data[3])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", ["%" . str_replace(['avenue', 'street', 'drive', 'road', '.'], ['ave', 'st', 'dr', 'rd', ''], strtolower($address)) . "%"])
                                    ->where('zip', $data[6])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', 'LIKE', substr($data[1], 0, 10) . "%")
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", ["%" . str_replace(['avenue', 'street', 'drive', 'road', '.'], ['ave', 'st', 'dr', 'rd', ''], strtolower($address)) . "%"])
                                    ->where('zip', $data[6])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', 'LIKE', substr($data[1], 0, 10) . "%")
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->where('zip', $data[6])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', 'LIKE', $data[1] . "%")
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", [substr($address, 0, 10) . '%'])
                                    ->where('zip', $data[6])
                                    ->first();
            }
            
            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[3], $data[5]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
            	if ($data[9] == "SUSPENDED") {
            		$skipCount++;
            		continue;
            	}
            	
            	$newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = $data[1];
                
                $facility->city = $data[3];
                $facility->state = $data[5];
                $facility->county = $data[4];
                $facility->zip = $data[6];

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }

                $facility->filename = $uniqueFilename;
                
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name);
                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }
            $facility->state_id = $data[0];
            $facility->operation_id = $data[51];
            
            $facility->address = $data[2];
            if ($data[52] <> "") {
                $facility->address2 = $data[52];
            }
            $facility->phone = $data[7];
            if ($data[10] <> "") {
                $facility->capacity = $data[10];
            }

            if ($data[50] <> "") {
                $facility->age_range = $data[50];
            }
            
            /* if ($data[29] <> "") {
                $facility->pricing = "Infant - " . $data[29] . "; ";
            } 
            if ($data[31] <> "") {
                $facility->pricing .= "Toddler - " . $data[31] . "; ";
            } 
            if ($data[35] <> "") {
                $facility->pricing .= "Preschool - " . $data[35] . "; ";
            } 
            if ($data[41] <> "") {
                $facility->pricing .= "School Age - " . $data[41] . "; ";
            } */

            $facility->transportation = $data[26] == 'Y' ? 'Yes' : 'No';
            if ($data[25]=="Y") {
                $facility->accreditation = 'Yes';
            }
            
            $facility->type = ucwords(strtolower($data[8])) . " Child Care";
			$facility->status = $data[9];
			$facility->subsidized = $data[11] == 'Y' ? 1 : 0;
			
            $facility->is_center = $data[8] == 'Certified' ? 0 : 1;
            $facility->approved = $data[8] == 'Certified' ? 2 : 1;

            if ($data[47] == "Y") {
                if (preg_match("/Serves Children with Special Needs/i",$facility->additionalInfo) == false) {
                    $facility->additionalInfo .= "Serves Children with Special Needs; ";
                }
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

            if ($data[12] <> "" ) {
                $facilityHour->monday = $data[12];
            }

            if ($data[13] <> "" ) {
                $facilityHour->tuesday = $data[13];
            }

            if ($data[14] <> "" ) {
                $facilityHour->wednesday = $data[14];
            }

            if ($data[15] <> "" ) {
                $facilityHour->thursday = $data[15];
            }

            if ($data[16] <> "" ) {
                $facilityHour->friday = $data[16];
            }

            if ($data[17] <> "" ) {
                $facilityHour->saturday = $data[17];
            }

            if ($data[18] <> "" ) {
                $facilityHour->sunday = $data[18];
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