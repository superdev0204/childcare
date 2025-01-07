<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;
use App\Models\Zipcodes;
use App\Models\Counties;

class FloridaDownloadedCsvParser extends Command
{
    protected $signature = 'custom:parse-florida-downloaded-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/florida/FloridaChildCare.csv';
    protected $filename = [];
    
    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                $this->info("skip " . ++$skipCount);
                continue;
            }
            
            if ($data[10] == "Unlicensed") {
                $skipCount++;
                continue;
            }
            
            if ($data[2] == "" && $data[7] == "") {
                $skipCount++;
                continue;
            }
            if (preg_match( "/Home/i",$data[8])) {
                $address = explode(" ", $data[2], 2);
                if ($address[1] != "") {
                    $data[2] = $address[1];
                }                
            }
            
            $this->info("test $row = " . $data[1]);
            
            if (strlen($data[1]) > 75) {
                $data[1] = substr($data[1], 0, 75);
            }

            if ($data[7] <> '' && strlen($data[7]) < 10) {
                if ($data[4] == "Broward") {
                    $data[7] = "(954) " . $data[7];
                }
                if ($data[4] == "Palm Beach") {
                    $data[7] = "(561) " . $data[7];
                }
                if ($data[4] == "Hillsborough") {
                    $data[7] = "(813) " . $data[7];
                }
                if ($data[4] == "Pinellas") {
                    $data[7] = "(727) " . $data[7];
                }
                if ($data[4] == "Sarasota") {
                    $data[7] = "(941) " . $data[7];
                }
            }

            if ($data[3] == "" && $data[6] <> "") {
                $zip = Zipcodes::where('zipcode', $data[6])->first();
                
                if ($zip) {
                    $data[3] = $zip->city;
                }
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'FL')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[12])
                                    ->where('state', 'FL')
                                    ->first();
            }

            if (!$facility && $data[6] <> "") {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility && $data[7] <> "") {
                $facility = Facility::where('name', trim($data[1]))
                                    ->where('phone', $data[7])
                                    ->first();
            }

            if (!$facility && $data[2] <> "" && $data[7] <> "") {
                $facility = Facility::where('address', $data[2])
                                    ->where('phone', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', 'LIKE', '%' . substr($data[7], -4))
                                    ->where('address', 'LIKE', substr($data[2], 0, 5) . '%')
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility && $data[2] <> "") {
                $facility = Facility::where('is_center', 1)
                                    ->where('address', $data[2])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[3], $data[5]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
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
            $facility->city = $data[3];
            $facility->county = $data[4];
            $facility->state = $data[5];
            $facility->zip = $data[6];
            $facility->phone = $data[7];

            $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();
            
            if ($city) {
                $facility->cityfile = $city->filename;
                $facility->county = $city->county;
            }
            
            $facility->state_id = $data[0];
            $facility->operation_id = $data[12];
            $facility->type = $data[8];

            if ($data[9] <> "") {
                $facility->capacity = $data[9];
            }

            $facility->status = $data[10];
            $facility->typeofcare = $data[20];
            $facility->accreditation = $data[21];

            if ($data[24] == 'Yes' && preg_match( "/VPK Provider/i",$facility->typeofcare)==false) {
                $facility->typeofcare = 'VPK Provider; ' . $facility->typeofcare;
            }

            if ($data[25] == 'Yes') {
                $facility->is_religious = 1;
            }

            if ($data[26] == 'Yes') {
                $facility->headstart = 1;
            }

            if ($data[27] == 'Yes') {
                $facility->subsidized = 1;
            }

            if (preg_match( "/Home/i",$data[8])) {
                $facility->is_center =  0;
                if ($facility->capacity >= 10) {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            if ($facility->user_id == 0) {
                $facility->daysopen = "Monday - Friday";
                if ($data[18] <> '') {
                    $facility->daysopen .= ', Saturday';
                }
                if ($data[19] <> '') {
                    $facility->daysopen .= ', Sunday';
                }
            }

            $facility->hoursopen = $data[13];

            if ($facility->county <> '') {
                $county = Counties::where('state', $facility->state)
                                ->where('county', $facility->county)
                                ->first();

                if ($county) {
                    $facility->district_office = $county->district_office . " <br/> ";
                    $facility->district_office .= $county->do_address . " <br/> ";
                    $facility->district_office .= $county->do_address2 ;
                    $facility->do_phone = $county->do_phone;
                }
            } else {
                $facility->district_office = 'Florida Dept of Children and Families (DCF)-  Child Care Services';
                $facility->do_phone = '(850) 488-4900';
            }

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[11] <> "" ) {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[11]));

                if (isset($facilityDetail->id)) {
                    $facilityDetail->save();
                } else {
                    DB::table('facilitydetail')->insert((array) $facilityDetail);
                }
            }

            if ($data[13] <> "" || $data[14] <> "") {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }

                if ($data[13] <> "" ) {
                    $facilityHour->monday = $data[13];
                }

                if ($data[14] <> "" ) {
                    $facilityHour->tuesday = $data[14];
                }

                if ($data[15] <> "" ) {
                    $facilityHour->wednesday = $data[15];
                }

                if ($data[16] <> "" ) {
                    $facilityHour->thursday = $data[16];
                }

                if ($data[17] <> "" ) {
                    $facilityHour->friday = $data[17];
                }

                if ($data[18] <> "" ) {
                    $facilityHour->saturday = $data[18];
                }

                if ($data[19] <> "" ) {
                    $facilityHour->sunday = $data[19];
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