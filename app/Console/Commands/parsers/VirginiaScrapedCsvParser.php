<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class VirginiaScrapedCsvParser extends Command
{
    protected $signature = 'custom:parse-virginia-scraped-csv';
    protected $description = 'Parse data using a specified parser';        
    protected $file = '/datafiles/virginia/VirginiaChildCare.csv';
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
                $this->info("skip " . ++$skipCount);
                continue;
            }

            $this->info("test $row = " . $data[1]);
            
            if ($data[7] == "Family Day Home" || $data[7] == "Voluntary Registration") {
                $address = explode(" ", $data[1], 2);
                if ($address[1] != "") {
                    $data[1] = $address[1];
                }
            }
                        
            if (strlen($data[2]) > 5) {
                $data[2] = substr($data[2],0,5);
            }

            $facility = Facility::where('state_id', trim($data[16]))
                                ->where('state', 'VA')
                                ->where('type', $data[7])
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[1])
                                    ->where('zip', $data[2])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', 'like', substr($data[1],0,10) . '%')
                                    ->where('zip', $data[2])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where(function ($query) use ($data) {
                                        $query->where('phone', $data[6])
                                            ->orWhere('phone', str_replace(' ', '', $data[6]));
                                    })
                                    ->where('address', 'LIKE', substr($data[1], 0, 10) . '%')
                                    ->where('zip', $data[2])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [preg_replace("/[^0-9]/", "", $data[6])])
                                    ->where('address', $data[1])
                                    ->where('zip', $data[2])
                                    ->first();
            } 
            
            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[6])
                                    ->where('zip', $data[2])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', str_replace(' ', '',$data[6]))
                                    ->where('zip', $data[2])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[3], 'va');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->city = $data[3];
                $facility->county = $data[4];
                $facility->state = "VA";
                
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
                $this->info("existing $existCount " . $facility->name . " | " . $data[0]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->state_id = $data[16];
            $facility->operation_id = $data[21];
            $facility->type = $data[7];
            $facility->name = $data[0];
            $facility->address = $data[1];
            $facility->address2 = $data[18];
            $facility->zip = $data[2];
            $facility->phone = $data[6];
            
            if ($data[7] == "Family Day Home" || $data[7] == "Voluntary Registration") {
                $facility->is_center =  0;
                if ($data[7] == "Family Day Home") {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }
            
            if ($data[11] <> "") {
                $facility->hoursopen = $data[11];
            }
            if ($data[12] <> "") {
                $facility->daysopen = $data[12];
            }
            if ($data[13] <> "") {
                $facility->capacity = $data[13];
            }
            if ($data[14] <>"") {
                $facility->age_range = $data[14];
            }
            if ($data[17] == "Yes") {
                $facility->subsidized = 1;
            }
            if ($data[19] <> "") {
                $facility->state_rating = $data[19];
            }
            if ($data[10] <> "") {
                $contactName = explode(" ", $data[10], 2);
                $facility->contact_firstname = $contactName[0];
                $facility->contact_lastname = $contactName[1];
            }

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }
            
            $facility->district_office = 'Virginia Dept of Social Services - Division of Licensing Programs';
            $inspector = explode(":", $data[15]);
            $facility->licensor = $inspector[0];
            if ($inspector[1] <> "") {
                $facility->do_phone = trim($inspector[1]);
            } else {
                $facility->do_phone = '(800) 543-7545';
            }
            
            $facility->ludate = date('Y-m-d H:i:s');

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }
            
            if ($data[9] <> "") {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();
                
                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }
                
                $facilityDetail-> current_license_expiration_date = date('Y-m-d', strtotime($data[9]));
                
                if (isset($facilityDetail->id)) {
                    $facilityDetail->save();
                } else {
                    DB::table('facilitydetail')->insert((array) $facilityDetail);
                }
            }
            
            if ($data[11] <> "") {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();
                
                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }
                
                $facilityHour->monday = substr($data[11],0,80);
                $facilityHour->tuesday = substr($data[11],0,80);
                $facilityHour->wednesday = substr($data[11],0,80);
                $facilityHour->thursday = substr($data[11],0,80);
                $facilityHour->friday = substr($data[11],0,80);
                
                if (preg_match("/saturday/i",$data[12])) {
                    $facilityHour->saturday = substr($data[11],0,80);
                }elseif (preg_match("/sunday/i",$data[12])) {
                    $facilityHour->saturday = substr($data[11],0,80);
                    $facilityHour->sunday = substr($data[11],0,80);
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