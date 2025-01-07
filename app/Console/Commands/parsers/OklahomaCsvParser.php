<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class OklahomaCsvParser extends Command
{
    protected $signature = 'custom:parse-oklahoma-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/oklahoma/OklahomaChildCare.csv';
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

            if ($data[2] == "Can't find field by attr") {
                print "skip " . ++$skipCount . "\n";
                continue;
            }

            $this->info("test $row = " . $data[0]);

            if ($data[16] == "Home") {
                $address = explode(" ", $data[4], 2);
                if ($address[1] != "") {
                    $data[4] = $address[1];
                }
            }

            $facility = Facility::where('state_id', trim($data[0]))
                                ->where('state', 'OK')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', trim($data[0]))
                                    ->where('state', 'OK')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[2])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[7])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[2])
                                    ->where('phone', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[5], 'ok');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[2];
                $facility->address = $data[4];
                $facility->city = $data[5];
                $facility->state = "OK";
                $facility->zip = $data[6];
                $facility->phone = $data[7];
                $facility->county = $data[12];
                $facility->user_id = null;
                $facility->hoursopen = '';
                $facility->daysopen = '';
                $facility->additionalInfo = '';

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
            $facility->operation_id = $data[0];
            $facility->email = $data[8];

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if ($data[16] == "Center" || $data[16] == "Child Care Program") {
                $facility->type = "Child Care Center";
                $facility->is_center =  1;
            } else {
                $facility->type = "Family Child Care Home";
                $facility->is_center =  0;
                if ($data[17] >= 12) {
                    $facility->type = "Large Family Child Care Home";
                    $facility->approved = 2;
                }
            }
            $facility->capacity = $data[17];
            $facility->subsidized = ($data[18] == "Yes" ? 1 : 0);
            $facility->age_range = $data[19];
            $facility->typeofcare = $data[20];
            $facility->transportation = $data[21];

            if ($data[3] <> "") {
                $contactName = explode(" ", $data[3], 2);
                $facility->contact_firstname = $contactName[0];
                $facility->contact_lastname = $contactName[1];
            }

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }

            if ($data[11] <> "") {
                $stars = str_replace(" Stars","",$data[11]);
                $stars = str_replace(" Star","",$stars);
                $facility->state_rating = $stars;
                if (preg_match("/star/i",$facility->additionalInfo) == false) {
                    $facility->additionalInfo .= " Rated " . $data[11] . ".";
                }
            }

            $facility->district_office = ' Oklahoma Dept of Human Services - Child Care Services';
            $facility->licensor = $data[13];

            if ($data[14] <> "") {
                $facility->do_phone = $data[14];
            } else {
                $facility->do_phone = '(405) 521-3561';
            }

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }
            
            if ($data[10] <> "") {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();
                
                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }
                
                $facilityDetail->fax = $data[10];
                
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