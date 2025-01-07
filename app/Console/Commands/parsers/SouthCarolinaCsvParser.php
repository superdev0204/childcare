<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class SouthCarolinaCsvParser extends Command
{
    protected $signature = 'custom:parse-south-carolina-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/south_carolina/SouthCarolinaChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $existCount = 0;
        $newCount = 0;
        $skipCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($this->handle, 1000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[0]);
            
            if ($data[13] == "Family Child Care Home") {
                $address = explode(" ", $data[2], 2);
                if ($address[1] != "") {
                    $data[2] = $address[1];
                }
            }
            
            $data[4] = str_replace(" County","",$data[4]);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'SC')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[9])
                                    ->where('state', 'SC')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[7])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[7])
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

                $facility->name = $data[1];
                $facility->address = $data[2];
                $facility->city = trim($data[3]);
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

                $facility->approved = 1;
                $facility->filename = $uniqueFilename;
            } else {
                $existCount++;
                if ($facility->approved == -1) {
                    $facility->approved = 1;
                }
                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
                $this->info("existing $existCount " . $facility->name . " | " . $data[1]);
            }

            $facility->state_id = $data[0];
            $facility->operation_id = $data[9];

            if ($data[12] <> "") {
                $contactName = explode(" ", $data[12], 2);
                $facility->contact_firstname = $contactName[0];
                $facility->contact_lastname = $contactName[1];
            }

            $facility->type = $data[13];
            if ($data[14] <> "") {
                $facility->capacity = $data[14];
            }

            $facility->district_office = "South Carolina Dept. of Social Services - Division of Child Care Services";
            $facility->do_phone = $data[22];
            $facility->licensor = $data[23];

            if ($data[13] == "Child Care Center") {
                $facility->is_center = 1;
            } else {
                $facility->is_center = 0;
            }

            if ($data[13] == "Group Child Care Home") {
                $facility->approved = 2;
            }

            if ($data[24] <> "") {
                $facility->state_rating = $data[24];
                /* if (preg_match("/ABC Quality Level/i",$facility->additionalInfo) == false) {
                    $facility->additionalInfo .= " ABC Quality Level: " . $stars . ";";
                } */
            }
            //$facility->highlight = $data[20];
            
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

            if ($data[10] <> "") {
                $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[10]));
            }

            if ($data[11] <> "") {
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[11]));
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

            if ($data[15] <> "") {
                $facilityHour->sunday = $data[15];
            }
            if ($data[16] <> "") {
                $facilityHour->monday = $data[16];
            }
            if ($data[17] <> "") {
                $facilityHour->tuesday = $data[17];
            }
            if ($data[18] <> "") {
                $facilityHour->wednesday = $data[18];
            }
            if ($data[19] <> "") {
                $facilityHour->thursday = $data[19];
            }
            if ($data[20] <> "") {
                $facilityHour->friday = $data[20];
            }
            if ($data[21] <> "") {
                $facilityHour->saturday = $data[21];
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