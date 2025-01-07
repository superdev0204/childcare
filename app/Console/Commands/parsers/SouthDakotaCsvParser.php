<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;
use App\Models\Counties;

class SouthDakotaCsvParser extends Command
{
    protected $signature = 'custom:parse-south-dakota-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/south-dakota/SouthDakotaChildCare.csv';
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
                $this->info("skip " . ++$skipCount);
                continue;
            }

            $this->info("test $row = " . $data[1]);
            
            if ($data[2] == "Family Child Care (capacity 1-12)") {
                $address = explode(" ", $data[7], 2);
                if ($address[1] != "") {
                    $data[7] = $address[1];
                }
            }

            if (strlen($data[13]) > 5) {
                $data[6] = substr($data[6],0,5);
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'SD')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', trim($data[24]))
                                    ->where('state', 'SD')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[7])
                                    ->where('zip', $data[6])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('phone', $data[8])
                                    ->where('address', $data[7])
                                    ->where('zip', $data[6])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[5], 'sd');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
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

            $facility->state_id = $data[0];
            $facility->operation_id = $data[24];
            $facility->name = $data[1];
            $facility->type = $data[2];
            $facility->state = $data[3];
            $facility->county = $data[4];
            $facility->city = $data[5];
            $facility->zip = $data[6];
            $facility->address = $data[7];
            $facility->phone = $data[8];
            $facility->email = $data[9];
            $facility->website = $data[10];

            if ($data[11] <> '') {
                $facility->capacity = $data[11];
            }

            $city = Cities::where('state', $facility->state)
                        ->where('city', $facility->city)
                        ->first();

            if ($city) {
                $facility->cityfile = $city->filename;
                $facility->county = $city->county;
            }

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if (preg_match("/Family/i", $data[2])) {
                $facility->is_center =  0;
                if ($data[2] == "Group Family Child Care (capacity 13-20)") {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }
            if ($data[16] <> "Not Reported") {
                $facility->hoursopen = $data[16];
            }

            if ($facility->county <> '') {
                $county = Counties::where('state', $facility->state)
                                ->where('county', $facility->county)
                                ->first();

                if ($county) {
                    $facility->district_office = $county->district_office;
                    $facility->do_phone = $county->do_phone;
                }
            }

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }


            if ($data[16] <> "Not Reported") {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }

                if ($data[16] <> "" ) {
                    $facilityHour->monday = $data[16];
                }

                if ($data[17] <> "" ) {
                    $facilityHour->tuesday = $data[17];
                }

                if ($data[18] <> "" ) {
                    $facilityHour->wednesday = $data[18];
                }

                if ($data[19] <> "" ) {
                    $facilityHour->thursday = $data[19];
                }

                if ($data[20] <> "" ) {
                    $facilityHour->friday = $data[20];
                }

                if ($data[21] <> "" ) {
                    $facilityHour->saturday = $data[21];
                }

                if ($data[22] <> "" ) {
                    $facilityHour->sunday = $data[22];
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