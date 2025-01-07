<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class GeorgiaCsvParser extends Command
{
    protected $signature = 'custom:parse-georgia-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/georgia/GeorgiaChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 5000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[1]);

            if ($data[3] == "Family Child Care Learning Home") {
                $address = explode(" ", $data[5], 2);
                if ($address[1] != "") {
                    $data[5] = $address[1];
                }
            }

            $facility = Facility::where('state_id', $data[1])
                                ->where('state', 'GA')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[1])
                                    ->where('state', 'GA')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[2]))
                                    ->where('phone', $data[18])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[2]))
                                    ->where('address', $data[5])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[18])
                                    ->where('address', $data[5])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[6], $data[10]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->city = $data[6];
                $facility->state = $data[10];
                $facility->county = $data[7];
                if ($data[40] != null) {
                    $facility->hoursopen = $data[40] . " - " . $data[41];
                }

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[2]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->name = substr($data[2], 0, 80);
            $facility->address = $data[5];
            $facility->zip = $data[9];
            $facility->phone = $data[18];
            $facility->type = $data[3];
            $facility->is_center = ($facility->type == "Family Child Care Learning Home" || $facility->type == "Group Day Care Home") ? 0: 1;

            if ($data[52] <> "" && $data[52] != "Unknown") {
	            $facility->accreditation = $data[52];
            }
            if ($data[37] <> '') {
	            $facility->capacity = $data[37];
            }
            $facility->state_id = $data[1];
            $facility->operation_id = $data[1];

            if ($facility->hoursopen == "" && $data[40] != null) {
                $facility->hoursopen = $data[40] . " - " . $data[41];
            }
            if ($facility->daysopen == "") {
                if ($data[39] == "MO TU WE TH FR") {
                    $facility->daysopen =  "Monday - Friday" ;
                } elseif ($data[39] == "MO TU WE TH FR SA") {
                    $facility->daysopen =  "Monday - Friday, Saturday";
                } elseif ($data[39] == "MO TU WE TH FR SA SU") {
                    $facility->daysopen =  "Monday - Friday, Saturday, Sunday";
                } else {
                    $facility->daysopen =  $data[39];
                }
            }

            if ($data[12] <> "") {
            	$facility->lat = $data[12];
            }
            if ($data[13] <> "") {
 				$facility->lng = $data[13];
            }
            $facility->email = $data[20];
            $facility->website = $data[21];
            $facility->contact_firstname = $data[22];
            $facility->contact_lastname = $data[23];
            
            $facility->age_range = $data[28];
            $facility->typeofcare = $data[29];
            if ($data[34] <> "") {
            	$facility->typeofcare = $data[34] . "; " . $facility->typeofcare;
            }
            if ($data[31] !== "") {
            	$facility->transportation = $data[31];
            }
            
            if (preg_match("/Infant/i",$facility->age_range)) {
                $facility->is_infant = 1;
            }
            if (preg_match("/Toddler/i",$facility->age_range)) {
                $facility->is_toddler = 1;
            }
            if (preg_match("/Preschool/i",$facility->age_range)) {
                $facility->is_preschool = 1;
            }
            if (preg_match("/School Age/i",$facility->age_range)) {
                $facility->is_afterschool = 1;
            }
            
            if (preg_match("/CAPS/i",$data[33])) {
            	$facility->subsidized =  1;
            }
            if (preg_match("/Religion-based/i",$data[33])) {
            	$facility->is_religious = 1;
            }
            if (preg_match("/Summer Camp/i",$data[34])) {
            	$facility->is_camp = 1;
            }
            if (preg_match("/Head Start/i",$data[34])) {
            	$facility->headstart = 1;
            }
            
            if ($facility->approved <= 0) {
                $facility->approved = 1;
                if ($facility->type == "Group Day Care Home") {
                    $facility->approved = 2;
                }
            }

            if (preg_match("/Evening/i",$data[33]) && preg_match("/Has Evening Care/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= " Has Evening Care;";
            }

            if (preg_match("/Drop in/i",$data[32]) && preg_match("/Has Drop In Care/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= " Has Drop In Care;";
            }

            if (preg_match("/Summer Camp/i",$data[34]) && preg_match("/Has School Age Summer Care/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= " Has School Age Summer Care;";
            }

            if (preg_match("/After School Only/i",$data[33]) && preg_match("/Has School Age Only/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= " Has School Age Only;";
            }

            if (preg_match("/Cacfp/i",$data[33]) && preg_match("/Has Cacfp/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= " Has Cacfp;";
            }

            if ($data[44] !== "" && preg_match("/Financial Info/i",$facility->additionalInfo) == false) {
            	$facility->additionalInfo .= " Financial Info: " . $data[44] . ";";
            }
            
            if ($data[54] !== "" && preg_match("/QualityRated_Participant/i",$facility->accreditation) == false) {
                $facility->accreditation = "QualityRated_Participant; " . $facility->accreditation;
            }

            if ($data[45] <> "") {
            	$facility->pricing = $data[45];
            }
            
            if ($data[54] <> "") {
                $facility->state_rating = $data[54];
            }

            if ($data[49] <> "") {
            	$facility->language = $data[49];
            }
            
            # wrong - donot use GA Department
            #$facility->district_office = 'Georgia Department of Early Care and Learning';
            #$facility->do_phone = '404-657-5562';

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

            if ($data[14] <> "") {
                $facilityDetail->mailing_address = $data[14];
            }

            if ($data[15] <> "") {
                $facilityDetail->mailing_city = $data[15];
            }

            if ($data[17] <> "") {
                $facilityDetail->mailing_state = $data[17];
            }

            if ($data[16] <> "") {
                $facilityDetail->mailing_zip = $data[16];
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
            
            if ($data[39] !== "" && preg_match("/MO/i",$data[39]) && $data[40] <> "") {
            	#print $data[16] . " " . $data[17] . " ";
            	$facilityHour->monday = substr($data[40], 0,8) . " - " . substr($data[41], 0,8);
            }
            
            if ($data[39] !== "" && preg_match("/TU/i",$data[39]) && $data[40] <> "") {
            	$facilityHour->tuesday = substr($data[40], 0,8) . " - " . substr($data[41], 0,8);
            }
            
            if ($data[39] !== "" && preg_match("/WE/i",$data[39]) && $data[40] <> "") {
            	$facilityHour->wednesday = substr($data[40], 0,8) . " - " . substr($data[41], 0,8);
            }
            
            if ($data[39] !== "" && preg_match("/TH/i",$data[39]) && $data[40] <> "") {
            	$facilityHour->thursday = substr($data[40], 0,8) . " - " . substr($data[41], 0,8);
            }
            
            if ($data[39] !== "" && preg_match("/FR/i",$data[39]) && $data[40] <> "") {
            	$facilityHour->friday = substr($data[40], 0,8) . " - " . substr($data[41], 0,8);
            }
            
            if ($data[39] !== "" && preg_match("/SA/i",$data[39]) && $data[40] <> "") {
            	$facilityHour->saturday = substr($data[40], 0,8) . " - " . substr($data[41], 0,8);
            }
            
            if ($data[39] !== "" && preg_match("/SU/i",$data[39]) && $data[40] <> "") {
            	$facilityHour->sunday = substr($data[40], 0,8) . " - " . substr($data[41], 0,8);
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