<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class MassachusettsCsvParser extends Command
{
    protected $signature = 'custom:parse-massachusetts-csv';
    protected $description = 'Parse data using a specified parser';        
    protected $file = '/datafiles/massachusetts/MassachusettsChildCareOld.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 3000, ";")) !== FALSE) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[2]);
            
            if ($data[9] == "Family Child Care") {
                $address = explode(" ", $data[13], 2);
                if ($address[1] != "") {
                    $data[13] = $address[1];
                }
            }
            
            $data[7] = str_replace("'", "", $data[7]);
            $data[12] = str_replace("'", "", $data[12]);
            $data[16] = str_replace("'", "", $data[16]);
            
            if (strlen($data[16]) == 4) {
                $data[16] = "0" . $data[16];
            } else {
                $data[16] = substr($data[16],0,5);
            }

            $facility = Facility::where('state_id', $data[1])
                                ->where('state', 'MA')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[1])
                                    ->where('state', 'MA')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[2]))
                                    ->where('phone', $data[12])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[14], $data[15]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[2];
                $facility->address = $data[13];
                $facility->city = $data[14];
                $facility->state = $data[15];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[2]);
                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }

            if ($data[4] <> "" && preg_match("/Provider First Licensed/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Provider First Licensed on: " . $data[4] . ".  ";
            }

            $facility->state_id = $data[1];
            $facility->operation_id = $data[1];

            if ($data[3]<> "") {
                $facility->capacity = $data[3];
            }

            $facility->type = $data[9];
            if ($facility->type == "Family Child Care") {
                $facility->is_center = 0;
                if ($facility->capacity > 6) {
                    $facility->approved = 2;
                }
            } else {
                $facility->is_center = 1;
            }

            $facility->contact_firstname = $data[10];
            $facility->contact_lastname = $data[11];
            $facility->phone = $data[12];
            $facility->zip = $data[16];

            $facility->daysopen = "Monday-Friday";
 
            $facility->district_office =  $data[6];
            $facility->do_phone =  $data[7];
            $facility->licensor =  $data[8];

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[4] <> "" || $data[5] <> "") {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[4] <> "") {
                    $expDates = explode("/", $data[4], 3);
                    $facilityDetail->initial_application_date = $expDates[2] . "-" . $expDates[0] . "-" . $expDates[1];
                }

                if ($data[5] <> "") {
                    $expDates = explode("/", $data[5], 3);
                    $facilityDetail->current_license_begin_date = $expDates[2] . "-" . $expDates[0] . "-" . $expDates[1];
                }

                if (isset($facilityDetail->id)) {
                    $facilityDetail->save();
                } else {
                    DB::table('facilitydetail')->insert((array) $facilityDetail);
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