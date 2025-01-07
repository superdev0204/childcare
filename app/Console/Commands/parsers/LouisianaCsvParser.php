<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class LouisianaCsvParser extends Command
{
    protected $signature = 'custom:parse-louisiana-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/louisiana/LouisianaChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $existCount = 0;
        $newCount = 0;
        $skipCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 4000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }
            if ($data[4] == "" && $data[5] == "" && $data[7] == "") {
                $skipCount++;
                continue;
            }
            
            $this->info("test $row = " . $data[0]);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'LA')
                                ->first();

            if (!$facility && $data[21]) {
                $facility = Facility::where('operation_id', $data[21])
                                    ->where('state', 'LA')
                                    ->first();
            }

            if (!$facility && $data[2] && $data[8]) {
                $facility = Facility::where('name', $data[2])
                                    ->where('phone', $data[8])
                                    ->first();
            }

            if (!$facility && $data[2] && $data[4] && $data[7]) {
                $facility = Facility::where('name', $data[2])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility && $data[8] && $data[4] && $data[7]) {
                $facility = Facility::where('phone', $data[8])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility && $data[8] && $data[7]) {
                $facility = Facility::where('phone', $data[8])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[5], 'la');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[2];
                $facility->address = $data[4];
                $facility->city = trim($data[5]);
                $facility->state = $data[6];
                $facility->zip = $data[7];
                $facility->phone = $data[8];
                $facility->county = str_replace(" Parish", "", $data[18]);

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
            $facility->operation_id = $data[21];
            $facility->type = $data[3];
            if ($data[20]) {
                $facility->type .= " - " . $data[20];
            }
            
            if ($data[20] == "R (Family Home)") {
                $facility->is_center =  0;
                $facility->approved = 2;
            } else  {
                $facility->approved = 1;
                $facility->is_center =  1;
            }
            
            if ($data[9] <> "" && $facility->email == "") {
                $facility->email = $data[9];
            }
            if ($facility->website == "") {
                if ($data[15]) {
                    $facility->website = $data[15];
                } elseif ($data[17]) {
                    $facility->website = $data[17];
                }
            } 
            
            if($data[19] <> "" && $data[19] <> "Information Coming Soon!") {
                $contactName = explode(" ", $data[19], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }
            
            if ($data[22] <> "") {
                $facility->transportation = $data[22];
            }
            if ($data[23] == 'Yes' && $facility->user_id=="") {
                $facility->typeofcare .= 'Before School Care; ';
            }
            if ($data[24] == 'Yes' && $facility->user_id=="") {
                $facility->typeofcare .= 'After School Care; ';
            }
            
            if ($data[25] <> "YES") {
                $facility->subsidized = 1;
            }
            if ($data[27] <> "") {
                $facility->age_range = $data[27];
            }
            if ($data[29] <> "") {
                $facility->capacity = $data[29];
            } elseif ($data[28]) {
                $facility->capacity = $data[28];
            }
            
            if ($data[26] <> "" && $data[26] <> "No Star Rating") {
                $facility->state_rating = str_replace(" Star(s)","",$data[26]);
                if (preg_match("/Quality Start Child Care/i",$facility->additionalInfo) == false) {
                    $facility->additionalInfo .= "Rated " . $data[26] . " by Quality Start Child Care Rating System; ";
                }
            }
            
            if ($data[30] <> "" && preg_match("/Academic Offerings/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Academic Offerings: " . $data[30] . "; ";
            }
            
            $facility->district_office = 'Louisiana Department of Children and Family Services - Licensing Section';
            $facility->do_phone = '(225) 342-9905';

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[10] <> "") {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }

                if ($data[10] <> "" ) {
                    $facilityHour->monday = $data[10];
                }

                if ($data[11] <> "" ) {
                    $facilityHour->tuesday = $data[11];
                }

                if ($data[12] <> "" ) {
                    $facilityHour->wednesday = $data[12];
                }

                if ($data[13] <> "" ) {
                    $facilityHour->thursday = $data[13];
                }

                if ($data[14] <> "" ) {
                    $facilityHour->friday = $data[14];
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