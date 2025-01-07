<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class RhodeIslandCsvParser extends Command
{
    protected $signature = 'custom:parse-rhode-island-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/rhode-island/RhodeIslandChildCare.csv';
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

            if ($data[2] == "") {
                $data[2] = $data[34];
                $data[4] = $data[35];
                $data[5] = $data[36];
            }
            $data[5] = str_pad($data[5], 5, "0", STR_PAD_LEFT);

            if ($data[6] <> "") {
                $fphoneNumber = preg_replace("/[^0-9]/", "", $data[6]);
                $data[6] = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $fphoneNumber);
            }
            $this->info("test $row = " . $data[1]);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'RI')
                                ->where('name', $data[1])
                                ->first();
            
            if (!$facility && $data[6] <> "") {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[6])
                                    ->first();
            }

            if (!$facility && $data[2] <> "") {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility && $data[2] <> "" && $data[6] <> "") {
                $facility = Facility::where('phone', $data[6])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility && $data[2] <> "") {
                $facility = Facility::where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }
            
            if (!$facility && $data[2] <> "") {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', 'like', substr($data[2], 0, 6) .'%')
                                    ->where('zip', $data[5])
                                    ->first();
            }
            
            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[4], 'ri');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = $data[1];
                $facility->address = $data[2];
                $facility->city = $data[4];
                $facility->state = $data[3];
                $facility->zip = $data[5];
                
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
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            if ($facility->approved < 1) {
                $facility->approved = 1;
            }

            $facility->state_id = $data[0];
            if ($data[7] <> "") {
                $facility->accreditation = $data[7];
            }
            
            if ($data[9]<> "") {
                $facility->capacity = $data[9];
            }
            if ($data[6]<> "") {
                $facility->phone = $data[6];
            }
            
            if ($data[12] <> "") {
                $contactName = explode(" ", $data[12], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }
            if (preg_match("/Home/i",$data[10])) {
                $facility->is_center = 0;
                $facility->type = "Child Care Home";
                if ($facility->capacity> 6) {
                    $facility->approved = 2;
                }
            } else {
                $facility->is_center = 1;
                $facility->type = "Child Care Center";
            }
            
            if ($data[17]<>"") {
                $facility->language = $data[17];
            }
            if ($data[18]<>"") {
                $facility->state_rating = $data[18];
            }
            if ($data[21] == "Yes") {
                $facility->subsidized = 1;
            }
            if ($data[23] <> "") {
                $facility->website = $data[23];
            }
            if ($data[24] <> "" and $facility->introduction=="") {
                $facility->introduction = $data[24];
            }
            if ($data[32] <> "") {
                $facility->age_range = $data[32];
            }
            if ($data[33] <> "" && $facility->email=="") {
                $facility->email = $data[33];
            }
            $facility->district_office = "Rhode Island DCYF - Day Care Licensing Unit";
            $facility->do_phone = "401-528-3624";

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

           
            if ($data[25] <> "") {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();
                
                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }
                
                $facilityHour->monday = $data[25];
                $facilityHour->tuesday = $data[26];
                $facilityHour->wednesday = $data[27];
                $facilityHour->thursday = $data[28];
                $facilityHour->friday = $data[29];
                $facilityHour->saturday = $data[30];
                $facilityHour->sunday = $data[31];
                
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