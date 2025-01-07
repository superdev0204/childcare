<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class IowaScrapedCsvParser extends Command
{
    protected $signature = 'custom:parse-iowa-scraped-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/iowa/IAChildCare.csv';
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
                continue;
            }

            print "test $row = " . $data[0] . "\n";
            if (preg_match("/Home/i",$data[16])) {
                $address = explode(" ", $data[1], 2);
                if ($address[1] != "") {
                    $data[1] = $address[1];
                }
            }
            
            $stateId = str_replace("https://ccmis.dhs.state.ia.us/ClientPortal/ProviderDetails.aspx?PID=", "", $data[30]);

            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[11]);
            
            if(strlen($strippedPhoneNumber) == 10) {
                $strippedPhoneNumber = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $strippedPhoneNumber);
            }

            $this->info("test $row = " . $data[1]);

            $facility = Facility::where('state_id', $stateId)
                                ->where('state', 'IA')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $stateId)
                                    ->where('state', 'IA')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[1])
                                    ->where('city', $data[3])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[11])
                                    ->where('address', $data[1])
                                    ->where('city', $data[3])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $strippedPhoneNumber)
                                    ->where('address', $data[1])
                                    ->where('city', $data[3])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[1])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[11])
                                    ->where('address', $data[1])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $strippedPhoneNumber)
                                    ->where('address', $data[1])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[11])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $strippedPhoneNumber)
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[11])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $strippedPhoneNumber)
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[3], 'ia');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[0];
                $facility->address = $data[1];
                $facility->address2 = $data[2];
                $facility->city = $data[3];
                $facility->state = $data[4];
                $facility->zip = $data[5];
                $facility->phone = $data[11];
                $facility->county = $data[29];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[0]);
                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            if ($data[20] <> "") {
                if (strlen($data[20])>5) {
                    $capacity = explode(" ", $data[20], 2);
                    $facility->capacity = $capacity[0];
                } else {
                    $facility->capacity = $data[20];
                }
            }

            if (strlen(trim($data[13])) > 2) {
                $contact = explode(", ", $data[13], 2);
                $facility->contact_lastname = $contact[0];
                $facility->contact_firstname = $contact[1];
            }

            if ($data[14] == "Canceled") {
                $facility->approved = -1;
            }

            $facility->state_id = $stateId;
            $facility->operation_id = $stateId;
            $facility->status = $data[14];

            $types = explode(",",$data[16]);
            $facility->type = array_pop($types);
            $this->info($facility->type);


            if ($data[17] == "Yes") {
                $facility->subsidized = 1;
            }

            if ($data[18] <> "") {
                $facility->state_rating = $data[18];
                if (preg_match("/QRS Level/i",$facility->additionalInfo) == false) {
                    $facility->additionalInfo .= "QRS Level: " . $data[18] . "; ";
                }
            }

            $facility->pricing = $data[28];

            if ($facility->user_id == 0) {
                if ($data[21]<> "" && $data[22]<> "") {
                    $facility->daysopen = "Monday - Friday";
                }
                if ($data[26]<> "") {
                    $facility->daysopen .= ", Saturday";
                }
                if ($data[27]<> "") {
                    $facility->daysopen .= ", Sunday";
                }
                $facility->hoursopen = $data[21];
            }

            if (preg_match("/Home/i",$facility->type)) {
                $facility->is_center =  0;
                if ($facility->capacity >= 12) {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            $facility->district_office = 'Iowa Department of Human Services - Child Care Assistance Unit';
            $facility->do_phone = '1-866-448-4605';

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[15] <> "" || $data[31] <> "" ) {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[15] <> "") {
                    $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[15]));
                }
                if ($data[31] <> "") {
                    $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[31]));
                }

                if ($data[6] <> "") {
                    $facilityDetail->mailing_address = $data[6];
                }
                if ($data[8] <> "") {
                    $facilityDetail->mailing_city = $data[8];
                }

                if ($data[9] <> "") {
                    $facilityDetail->mailing_state = $data[9];
                }

                if ($data[10] <> "") {
                    $facilityDetail->mailing_zip = $data[10];
                }

                if (isset($facilityDetail->id)) {
                    $facilityDetail->save();
                } else {
                    DB::table('facilitydetail')->insert((array) $facilityDetail);
                }
            }

            if ($data[21] <> "" || $data[22] <> "") {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }

                if ($data[21] <> "" ) {
                    $facilityHour->monday = $data[21];
                }

                if ($data[22] <> "" ) {
                    $facilityHour->tuesday = $data[22];
                }

                if ($data[23] <> "" ) {
                    $facilityHour->wednesday = $data[23];
                }

                if ($data[24] <> "" ) {
                    $facilityHour->thursday = $data[24];
                }

                if ($data[25] <> "" ) {
                    $facilityHour->friday = $data[25];
                }

                if ($data[26] <> "" ) {
                    $facilityHour->saturday = $data[26];
                }

                if ($data[27] <> "" ) {
                    $facilityHour->sunday = $data[27];
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