<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class NewJerseyCsvParser extends Command
{
    protected $signature = 'custom:parse-new-jersey-csv';
    protected $description = 'Parse data using a specified parser';        
    protected $file = '/datafiles/new-jersey/NewJerseyChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 4000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $data[8] = str_pad($data[8], 5, "0", STR_PAD_LEFT);
            $data[9] = str_replace("`", "", $data[9]);

            $this->info("test $row = " . $data[2]);

            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[10]);
            
            if(strlen($strippedPhoneNumber) == 10) {
                $data[10] = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $strippedPhoneNumber);    
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'nj')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[1])
                                    ->where('state', 'nj')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[2])
                                    ->where('address', $data[4])
                                    ->where('city', $data[6])
                                    ->where('state', 'nj')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('phone', $data[10])
                                    ->where('address', $data[4])
                                    ->where('city', $data[6])
                                    ->where('state', 'nj')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('state_id', $data[1])
                                    ->where('state', 'nj')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[2])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[8])
                                    ->first();
            }

            if (!$facility && $data[10] <> "") {
                $facility = Facility::where('phone', $data[10])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[8])
                                    ->first();
            }

            if (!$facility && $data[10] <> "") {
                $facility = Facility::where('name', $data[2])
                                    ->where('phone', $data[10])
                                    ->where('zip', $data[8])
                                    ->first();
            }

            if (!$facility && $data[10] <> "") {
                $facility = Facility::where('name', $data[2])
                                    ->where('phone', $data[10])
                                    ->where('city', trim($data[6]))
                                    ->first();
            }

            if (!$facility && $data[2] <> "") {
                $facility = Facility::where('name', $data[2])
                                    ->where('address', 'LIKE', substr($data[4], 0, 12) . '%')
                                    ->where('zip', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[6], 'nj');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[2];
                $facility->address = $data[4];
                $facility->address2 = $data[5];
                $facility->city = $data[6];
                $facility->state = $data[7];
                $facility->zip = $data[8];
                $facility->county = $data[9];
                $facility->phone = $data[10];

                $facility->is_center = 1;
                $facility->approved = 1;

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

            $facility->state_id = $data[0];
            $facility->operation_id = $data[1];
            $facility->type = $data[24] . " " . $data[3];
            if ($facility->email == "") {
                $facility->email = $data[12];
            }
            $facility->status = $data[13];
            if ($facility->website == "" && $data[14] <> "") {
                $facility->website = $data[14];
            }
            if ($data[15] <> "") {
                $facility->capacity = $data[15];
            }
            $facility->age_range = $data[16];
            $facility->contact_firstname = $data[17];
            $facility->contact_lastname = $data[19];
            $facility->state_rating = $data[20];
            if ($data[50]) {
                $facility->subsidized = $data[50];
            }
            $facility->district_office = "New Jersey Dept of Children and Families - Office of Licensing";
            $facility->do_phone = "1-877-667-9845";
            if ($data[46] <> "") {
                $facility->licensor = $data[46] . " " . $data[47] . " (" . $data[49] . ")";
                $facility->do_phone = $data[48];
            }
            
            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[23] <> "") {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();
                
                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[23]));
                
                if (isset($facilityDetail->id)) {
                    $facilityDetail->save();
                } else {
                    DB::table('facilitydetail')->insert((array) $facilityDetail);
                }
            }
            
            if ($data[25] <> "") {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();
                
                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }
                if ($data[25] == "Open") {
                    $facilityHour->monday = $data[26] . " - " . $data[27];
                }
                if ($data[28] == "Open") {
                    $facilityHour->tuesday = $data[29] . " - " . $data[30];
                }
                if ($data[31] == "Open") {
                    $facilityHour->wednesday = $data[32] . " - " . $data[33];
                }
                if ($data[34] == "Open") {
                    $facilityHour->thursday = $data[35] . " - " . $data[36];
                }
                if ($data[37] == "Open") {
                    $facilityHour->friday = $data[38] . " - " . $data[39];
                }
                if ($data[40] == "Open") {
                    $facilityHour->saturday = $data[41] . " - " . $data[42];
                }
                if ($data[43] == "Open") {
                    $facilityHour->sunday = $data[44] . " - " . $data[45];
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