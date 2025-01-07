<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class WisconsinCsvParser extends Command
{
    protected $signature = 'custom:parse-wisconsin-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/wisconsin/WisconsinChildCare.csv';
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

            if (preg_match("/Regular Certified|Licensed Family/",$data[10])) {
                $address = explode(" ", $data[3], 2);
                if ($address[1] != "") {
                    $data[3] = $address[1];
                }
            }
            
            $data[12] = preg_replace("/[^0-9]/", "", $data[12]);
            $data[6] = substr($data[6],0,5);
            
            $this->info("test $row = " . $data[2]);

            $facility = Facility::where('state_id', $data[27])
                                ->where('state', $data[6])
                                ->first();
            
            if (!$facility && $data[9] <> "N/A") {
                $facility = Facility::where('operation_id', $data[9])
                                    ->where('state', 'WI')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('state_id', 'like', $data[2] . '%')
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[12])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('address', $data[3])
                                    ->where('city', $data[4])
                                    ->where('phone', $data[12])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[4], 'wi');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $facility = new \stdClass();
                $facility->city = $data[4];
                $facility->state = "WI";
                
                $facility->county = str_replace(" County","",$data[7]);

                $newCount++;
                $this->info("new record $newCount");

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }

                $facility->approved = 1;
                $facility->filename = $uniqueFilename;
            }
            else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[0]);
                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            if (preg_match("/Certified|Licensed Family/",$data[10])) {
                $facility->is_center =  0;
                if (preg_match("/Licensed/",$data[10])) {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            $facility->name = $data[0];
            $facility->address = $data[3];
            $facility->zip = $data[6];
            $facility->phone = $data[12];
            
            $facility->state_id = $data[27];
            $facility->operation_id = $data[9];
            $facility->type = $data[10] . " Child Care";

            if ($data[16] > 0) {
                $facility->capacity = $data[16];
            } elseif ($data[28] > 0) {
                $facility->capacity = $data[28];
            }

            $facility->age_range =  $data[14];

            if ($facility->user_id == 0) {
                $facility->daysopen = "Monday-Friday";
                if ($data[25] <> "Closed") {
                    $facility->daysopen .= ", Saturday";
                }
                if ($data[26] <> "Closed") {
                    $facility->daysopen .= ", Sunday";
                }
            }

            if ($data[11] <> "") {
                $contactName = explode(" ", $data[11], 2);
                $facility->contact_firstname = $contactName[0];
                $facility->contact_lastname = $contactName[1];
            }

            if ($data[17] > 0 && preg_match("/Nighttime care available/",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Nighttime care available. ";
            }

            if ($data[18] <> "" && $data[18] <> "Jan-Dec" && $data[18] <> "Jan - Dec"  && preg_match("/Open /",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Open " . $data[18] . ". ";
            }

            if ($data[19] <> "") {
                $facility->accreditation = $data[19];
            }

            if ($data[15] <> "") {
                $facility->state_rating = str_replace(array(" star"," stars", ' Stars'),"",$data[15]);
            }

            $facility->district_office = 'Wisconsin Dept of Children and Families (DCF)-  Child Care Regulation and Licensing';
            $facility->do_phone = '608-266-9314';

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[20] <> "") {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }

                if ($data[20] <> "" ) {
                    $facilityHour->monday = $data[20];
                }

                if ($data[21] <> "" ) {
                    $facilityHour->tuesday = $data[21];
                }

                if ($data[22] <> "" ) {
                    $facilityHour->wednesday = $data[22];
                }

                if ($data[23] <> "" ) {
                    $facilityHour->thursday = $data[23];
                }

                if ($data[24] <> "" ) {
                    $facilityHour->friday = $data[24];
                }

                if ($data[25] <> "" ) {
                    $facilityHour->saturday = $data[25];
                }

                if ($data[26] <> "" ) {
                    $facilityHour->sunday = $data[26];
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