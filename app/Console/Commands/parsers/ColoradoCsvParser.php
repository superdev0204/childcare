<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class ColoradoCsvParser extends Command
{
    protected $signature = 'custom:parse-colorado-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/colorado/ColoradoChildCare.csv';
    protected $filename = [];
    
    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 8000, ";")) !== false) {
            $row++;

            if ($row <= 1) {
                $this->info("skip " . ++$skipCount);
                continue;
            }

            $this->info("test $row = " . $data[1]);
            
            $data[0] = trim($data[0]);
            $data[1] = trim($data[1]);
            $data[3] = trim($data[3]);
            $data[4] = trim($data[4]);

            $facility = Facility::where('state_id', $data[2])
                                ->where('state', 'CO')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[0])
                                    ->where('state', 'CO')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('state_id', $data[0])
                                    ->where('state', 'CO')
                                    ->first();
            }
            
            if (!$facility && $data[9] <> "") {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility && $data[9] <> "") {
                $facility = Facility::where('phone', $data[9])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('state_id', str_pad($data[0], 10, "0", STR_PAD_LEFT))
                                    ->where('state', 'CO')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('operation_id', str_pad($data[0], 10, "0", STR_PAD_LEFT))
                                    ->where('state', 'CO')
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[4], 'co');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                
                $facility->city = $data[4];
                $facility->state = $data[5];
                $facility->zip = $data[6];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[1]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }
            $facility->name = $data[1];
            $facility->address = $data[3];
            $facility->phone = $data[9];
            $facility->state_id = $data[2];
            $facility->operation_id = $data[0];
            $facility->type = $data[7];
            $facility->website = $data[10];
            $facility->status = $data[11];
            $facility->state_rating = $data[12];
            if ($data[13] == "Yes") {
                $facility->headstart = 1;
            }
            if ($data[14] == "Yes") {
                $facility->subsidized = 1;
            }
            
            if ($data[7]=="Family Child Care") {
                $facility->is_center =  0;
                if ($data[15] > 6) {
                    $facility->approved = 2;
                }
            } else {
                $facility->is_center =  1;
            }

            if ($data[15] <> "") {
                $facility->capacity = $data[15];
            }

            if ($data[17] <> "" && $data[17] <> "Home") {
                $facility->age_range =  $data[17];
            }
            $facility->language = $data[18];
            
            if ($data[26] <> "" && preg_match("/Special Needs/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Special Needs: " . $data[26] . ".";
            }

            $facility->district_office = 'Colorado Dept of Human Services - Division of Child Care';
            $facility->do_phone = '303-866-5958';

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[19] <> "" || $data[20] <> "") {
                $facilityDetail = Facilityhours::where('facility_id', $facility->id)->first();
                
                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }
                
                if ($data[19] <> "" ) {
                    $facilityHour->monday = $data[19];
                }
                
                if ($data[20] <> "" ) {
                    $facilityHour->tuesday = $data[20];
                }
                
                if ($data[21] <> "" ) {
                    $facilityHour->wednesday = $data[21];
                }
                
                if ($data[22] <> "" ) {
                    $facilityHour->thursday = $data[22];
                }
                
                if ($data[23] <> "" ) {
                    $facilityHour->friday = $data[23];
                }
                
                if ($data[24] <> "" ) {
                    $facilityHour->saturday = $data[24];
                }
                
                if ($data[25] <> "" ) {
                    $facilityHour->sunday = $data[25];
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