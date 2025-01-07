<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class DelawareCsvParser extends Command
{
    protected $signature = 'custom:parse-delaware-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/delaware/DelawareChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $starRating = [
            "Starting with Stars" => "1",
            "Two Stars" => "2",
            "Three Stars" => "3",
            "Four Stars" => "4",
            "Five Stars" => "5"
        ];

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 2000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[0]);

            if ($data[2] == "Licensed Family Child Care") {
                $address = explode(" ", $data[3], 2);
                if ($address[1] != "") {
                    $data[3] = $address[1];
                }
            }

            $facility = Facility::where('state_id', trim($data[0]))
                                ->where('state', 'DE')
                                ->where('name', $data[1])
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('name', trim($data[1]))
                                    ->where('phone', $data[9])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[9])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[5], $data[6]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                
                #$facility->address2 = $data[4];
                $facility->city = $data[5];
                $facility->county = $data[8];
                $facility->state = $data[6];

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

            $facility->name = $data[1];
            $facility->address = $data[3];
            $facility->zip = $data[7];
            $facility->phone = $data[9];
            $facility->type = $data[2];
            
            $facility->age_range = $data[10];
            $facility->capacity = $data[11];

            $facility->typeofcare = $data[14];
            $facility->state_id = $data[0];
            
            if ($data[2] == "Licensed Day Care Center" ||
            		$data[2] == "Licensed Child Care Center") {
                $facility->is_center =  1;
            } else  {
                $facility->is_center =  0;
            }
            
            if($data[2] == "Licensed Family Child Care") {
                $contactName = explode(", ", $data[1], 2);
                $facility->contact_lastname = $contactName[0];
                $facility->contact_firstname = $contactName[1];
            }

            if ($data[2] == "Licensed Large Family Child Care") {
                $facility->approved = 2;
            }
            
            if ($data[14] <> "" &&
                preg_match("/Regulated Services/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Regulated Services: " . $data[14] . "; ";
            }
            
            if ($data[4] <> "" &&
            	preg_match("/Special Conditions/i",$facility->additionalInfo) == false ) {
            			$facility->additionalInfo .= "Special Conditions: " . $data[4] . "; ";
            		}
            		
            if ($data[15] <> "" && preg_match("/Financial Arrangements/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Financial Arrangements: " . $data[15] . "; ";
            }
            
            if ($data[19] <> "" &&
            		$data[19] <> "No facility injuries reported and no facility deaths reported." &&
            		preg_match("/Reported Injuries & Deaths/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Reported Injuries & Deaths: " . $data[19] . "; ";
            } 

            if ($data[16] <> "") {
            	$facility->state_rating = $data[16];
            	if (preg_match("/Stars Rating/i",$facility->additionalInfo) == false) {
	                $facility->additionalInfo .= "Stars Rating: " . $data[17] . "; ";
            	}
                
            }

            $facility->district_office =  "State of Delaware, Office of Child Care Licensing";

            if ($facility->county =="New Castle") {
                $facility->do_phone =  "302-892-5800";
            } elseif ($facility->county =="Kent") {
                $facility->do_phone =  "302-739-5487";
            } elseif ($facility->county =="Sussex") {
                $facility->do_phone =  "302-739-5487";
            } else {
                $facility->do_phone =  "1-800-822-2236";
            }

            $facility->licensor =  "OCCL.DSCYF@state.de.us";

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

            $facilityHour->monday = $data[12] . " - " . $data[13];
            $facilityHour->tuesday = $data[12] . " - " . $data[13];
            $facilityHour->wednesday = $data[12] . " - " . $data[13];
            $facilityHour->thursday = $data[12] . " - " . $data[13];
            $facilityHour->friday = $data[12] . " - " . $data[13];

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