<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class OhioCsvParser extends Command
{
    protected $signature = 'custom:parse-ohio-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/ohio/OhioChildCare.csv';
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

            $this->info("test $row = " . $data[2]);

            $facility = Facility::where('state_id', $data[1])
                                ->where('state', 'OH')
                                ->first();
            
            if (!$facility && $data[1] <> "") {
                $facility = Facility::where('operation_id', $data[1])
                                    ->where('state', 'OH')
                                    ->first();
            }

            if (!$facility && $data[3] <> "") {
                $facility = Facility::where('name', $data[2])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility && $data[3] <> "" && $data[13]<> "") {
                $facility = Facility::where('phone', $data[13])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility && $data[13] <> "") {
                $facility = Facility::where('name', $data[2])
                                    ->where('phone', $data[13])
                                    ->first();
            }

            if (!$facility && $data[13]<> "" && $data[3] <> "") {
                $facility = Facility::where('address', $data[3])
                                    ->where('phone', $data[13])
                                    ->first();
            }

            if (!$facility && $data[3] <> "") {
                $facility = Facility::where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility && $data[3] <> "") {
                $facility = Facility::where('address', 'LIKE', substr($data[3], 0, 12) . '%')
                                    ->where('phone', 'LIKE', '%' . substr($data[13], -4))
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[4], 'oh');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[2];
                $facility->address = $data[3];
                $facility->city = $data[4];
                $facility->state = 'OH';
                $facility->zip = $data[6];
                $facility->county = $data[9];
                $facility->phone = $data[13];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[2]);
                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->state_id = $data[1];
            $facility->operation_id = $data[1];
            $facility->status = $data[7];
            $facility->type = $data[0];

            if($data[8] != "") {
                $contactName = explode(" ", $data[8], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }

            $facility->subsidized = ($data[27] <> "") ? 1 : 0;
            if ($data[15]<> "") {
	            $facility->accreditation = $data[15];
            }

            if (preg_match( "/Family Child Care Home/i",$data[0])) {
                $facility->is_center =  0;
                if (preg_match( "/Type A Family Child Care Home/i",$data[0])) {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            if ($data[16] <> "" && preg_match( "/Child Care Food Program/i",$facility->additionalInfo)==false) {
                $facility->additionalInfo = "This facility participates in the federally funded Child Care Food Program, and meets USDA nutrition requirements for meals and snacks; " . $facility->additionalInfo;
            }

            if ($data[17] <> "" && preg_match( "/Before School Care/i",$facility->additionalInfo)==false) {
                $facility->additionalInfo = "Before School Care Available; ";
            }

            if ($data[18] <> "" && preg_match( "/Evening Care/i",$facility->additionalInfo)==false) {
                $facility->additionalInfo = "Evening Care Available; ";
            }

            /* if ($data[19] == "Yes" && preg_match( "/Special Needs Child Care/i",$facility->additionalInfo)==false) {
                $facility->additionalInfo = "Special Needs Child Care Available; ";
            } */

            if ($data[21] <> "" && preg_match( "/After School Care/i",$facility->additionalInfo)==false) {
                $facility->additionalInfo = "After School Care Available; ";
            }

            if ($data[22] <> "" && preg_match( "/Overnight Care/i",$facility->additionalInfo)==false) {
                $facility->additionalInfo = "Overnight Care Available; ";
            }

            if ($data[20] <> "") {
                $facility->transportation = 'Yes; ';
            }
            if ($data[23] <> "") {
            	$facility->transportation .= 'Field Trips';
            }
            
            if ($data[25] == "Yes") {
                $facility->headstart = 1;
            }

            if ($data[0] =="Registered Day Camp" && preg_match( "/Registered Day Camps/i",$facility->additionalInfo)==false) {
                $facility->additionalInfo = "Registered Day Camps register with ODJFS and are effective yearly beginning March 16 of every year; " . $facility->additionalInfo;
            }

            if ($data[12] <> "") {
                $facility->state_rating = $data[12];
                if (preg_match( "/Quality Rating/i",$facility->additionalInfo)==false) {
	                $facility->additionalInfo = "Quality Rating: " . $data[12] . "; " . $facility->additionalInfo;
                }
            }

            if ($facility->user_id == 0 && $data[29] <> "" && $data[29] <> "NOT REPORTED") {
                $facility->daysopen = "Monday - Friday";
                $facility->hoursopen = $data[29];
                if ($data[34] <> "CLOSED" && $data[34] <> "" && $data[34] <> "NOT REPORTED") {
                    $facility->daysopen .= ", Saturday";
                }
                if ($data[35] <> "CLOSED" && $data[35] <> "" && $data[35] <> "NOT REPORTED") {
                    $facility->daysopen .= ", Sunday";
                }
            }

            $facility->district_office = 'Ohio Dept of Job and Family Services (ODJFS) - Division of Child Care';
            $facility->do_phone = '(877)302-2347';

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

            if ($data[10] <> "" ) {
                $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[10]));
            }

            if ($data[11] <> "" && $data[11] <> "Continuous") {
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[11]));
            } elseif ($data[11] == "Continuous") {
                $facilityDetail->current_license_expiration_date = "9999-12-31";
            }

            if ($data[16] == "Yes") {
                $facilityDetail->childcare_food_program = 1;
            } else {
                $facilityDetail->childcare_food_program = 0;
            }

            if (isset($facilityDetail->id)) {
                $facilityDetail->save();
            } else {
                DB::table('facilitydetail')->insert((array) $facilityDetail);
            }

            if ($data[29] <> "") {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }

                if ($data[29] <> "" ) {
                    $facilityHour->monday = $data[29];
                }

                if ($data[30] <> "" ) {
                    $facilityHour->tuesday = $data[30];
                }

                if ($data[31] <> "" ) {
                    $facilityHour->wednesday = $data[31];
                }

                if ($data[32] <> "" ) {
                    $facilityHour->thursday = $data[32];
                }

                if ($data[33] <> "" ) {
                    $facilityHour->friday = $data[33];
                }

                if ($data[34] <> "" ) {
                    $facilityHour->saturday = $data[34];
                }

                if ($data[35] <> "" ) {
                    $facilityHour->sunday = $data[35];
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