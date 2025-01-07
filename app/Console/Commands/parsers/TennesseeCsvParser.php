<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class TennesseeCsvParser extends Command
{
    protected $signature = 'custom:parse-tennessee-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/tennessee/TennesseeChildCare.csv';
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
            
            if ($data[2] == "Family Homes Care for 7 or fewer children" ) {
                $address = explode(" ", $data[3], 2);
                if ($address[1] != "") {
                    $data[3] = $address[1];
                }
            }
          
            if (preg_match("/(-E-)/i", $data[1])) {
                $data[1] = trim(str_replace(array("(-E-)"), "", $data[1]));
            }
            $data[8] = str_replace("'", "", $data[8]);
            $data[21] = str_replace("'", "", $data[21]);

            $this->info("test $row = " . $data[1]);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'TN')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[8])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[7])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('phone', $data[8])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[4], $data[6]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[1];
                $facility->address = $data[3];
                $facility->city = $data[4];
                $facility->state = $data[6];
                $facility->zip = $data[7];
                $facility->phone = $data[8];
                $facility->county = $data[5];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[1]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            if ($facility->approved <= 0) {
                $facility->approved = 1;
            }

            if ($data[2] == "Group Homes Care for 8-12 children") {
                $facility->approved = 2;
            }

            if ($data[2] == "Centers Care for 13 or more children" || $data[2] == "Drop In") {
                $facility->is_center =  1;
            } else  {
                $facility->is_center =  0;
            }

            $facility->type = $data[2];
            $facility->state_id = $data[0];

            if($data[9] != "") {
                $contactName = explode(" ", $data[9], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }

            if ($data[10] <> "" && preg_match("/star/i", $data[10])) {
                $facility->state_rating = substr($data[10], 0,1);
                if (preg_match("/Star/i",$facility->additionalInfo) == false) {
                    $facility->additionalInfo .= $data[10] . " " . $facility->type . "; ";
                }
            }

            if ($data[11]<>"") {
                $facility->capacity = $data[11];
            }

            $facility->age_range =  $data[12] . " to " . $data[13];
            $facility->hoursopen = $data[14] . " - " . $data[15];

            $facility->subsidized = ($data[16] == "YES") ? 1 : 0;

            if ($data[17] == "YES" && preg_match("/Discount/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Sibling Discount Available; ";
            }

            if ($data[18] == "YES" && preg_match("/Wheelchair Accessible/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Wheelchair Accessible; ";
            }

            $facility->transportation = $data[19];

            $facility->district_office = 'Tennessee Child Care Licensing';
            $facility->licensor = $data[20];

            if ($data[21] <> "") {
                $facility->do_phone = $data[8] = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1)$2-$3", $data[21]);
            } else {
                $facility->do_phone = '(615) 313-4778';
            }

            $facility->ludate = date('Y-m-d H:i:s');

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }
            
            if ($data[14] <> "") {
            	$facilityHour = Facilityhours::where('facility_id', $facility->id)->first();
            
            	if (!$facilityHour) {
            		$facilityHour = new \stdClass();
            		$facilityHour->facility_id = $facility->id;
            	}
            
            	$facilityHour->monday = $data[14] . " - " . $data[15];
            	$facilityHour->tuesday = $data[14] . " - " . $data[15];
            	$facilityHour->wednesday = $data[14] . " - " . $data[15];
            	$facilityHour->thursday = $data[14] . " - " . $data[15];
            	$facilityHour->friday = $data[14] . " - " . $data[15];
            	
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