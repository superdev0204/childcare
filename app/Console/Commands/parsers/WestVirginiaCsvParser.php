<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class WestVirginiaCsvParser extends Command
{
    protected $signature = 'custom:parse-west-virginia-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/west-virginia/WestVirginiaChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[1]);
            if (strlen($data[5])>5) {
            	$data[5] = substr($data[5], 0,5);
            }

            $facility = Facility::where('state_id', $data[10])
                                ->where('state', 'WV')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[7])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[4], 'wv');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[1];
                $facility->address = $data[2];
                $facility->address2 = $data[3];
                $facility->city = $data[4];
                $facility->state = "WV";
                $facility->zip = $data[5];
                $facility->county = $data[6];
                $facility->phone = $data[7];
                $facility->daysopen = '';
                $facility->hoursopen = '';
                $facility->user_id = null;

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[1]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->state_id = $data[0];
            
            if($data[11] != "") {
                $contactName = explode(" ", $data[11], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }

            if ($data[13] > 0) {
                $facility->capacity = $data[13];
            } else {
                $facility->capacity = 0;
            }

            $facility->age_range =  $data[14];

            if ($data[16] > 0) {
                $facility->capacity = $facility->capacity + $data[16];
                if ($data[19] > 0) {
                    $facility->capacity = $facility->capacity + $data[19];
                    if ($data[22] > 0) {
                        $facility->capacity = $facility->capacity + $data[22];
                        if ($data[25] > 0) {
                            $facility->capacity = $facility->capacity + $data[25];
                            $facility->age_range =  $facility->age_range . " - " . $data[27];
                        } else {
                            $facility->age_range =  $facility->age_range . " - " . $data[24];
                        }
                    } else {
                        $facility->age_range =  $facility->age_range . " - " . $data[21];
                    }

                } else {
                    $facility->age_range =  $facility->age_range . " - " . $data[18];
                }
            } else {
                $facility->age_range =  $facility->age_range . " - " . $data[15];
            }

            $facility->approved = 1;
            $facility->type = $data[29];
            $facility->status = $data[9];
            
            if ($facility->type == "Child Care Center" ||
            	$facility->type == "Head Start" ||
            		$facility->type == "Head Start/Child Care Center" ||
            		$facility->type == "Unlicensed School Age Child Care" ) {
            	$facility->is_center =  1;
            	if ($facility->type == "Child Care Center") {
	            	$facility->type = $data[9] . " " . $facility->type;
            	}
            	
            } else {
	            $facility->is_center =  0;
            	if ($facility->capacity>6) {
            		$facility->approved = 2;
            	}
            }   
            

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }

            $facility->district_office = 'West Virginia Dept of Health & Human Resources -  Division of Early Care and Education';
            $facility->do_phone = '(304) 558-1885';
            $facility->licensor = $data[8];

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[10] <> "" ) {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[10]));

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