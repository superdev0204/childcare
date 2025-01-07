<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class NevadaCsvParser extends Command
{
    protected $signature = 'custom:parse-nevada-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/nevada/nevada-childcare.csv';
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
                print "skip " . ++$skipCount . "\n";
                continue;
            }

            $this->info("test $row = " . $data[0]);
            
            $data[9] = substr($data[9],0,5);

            if (preg_match( "/FAMILY CARE/i",$data[1])) {
                $address = explode(" ", $data[6], 2);
                if ($address[1] != "") {
                    $data[6] = $address[1];
                }
            }

            $facility = Facility::where('state_id', $data[2])
                                ->where('state', 'NV')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('operation_id', $data[2])
                                    ->where('state', 'NV')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[6])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[6])
                                    ->where('city', $data[8])
                                    ->first();
            }

            if (!$facility && $data[10] <> "") {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[10])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility && $data[10] <> "") {
                $facility = Facility::where('phone', $data[10])
                                    ->where('address', $data[6])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility && $data[10] <> "") {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[10])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[8], 'nv');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[0];
                $facility->address = $data[6];
                $facility->city = $data[8];
                $facility->state = $data[7];
                $facility->zip = $data[9];
                $facility->phone = $data[10];
                $facility->county = $data[14];

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

            $facility->state_id = $data[2];
            $facility->operation_id = $data[2];
            $facility->type = $data[1];

            if ($facility->daysopen == "") {
                $facility->daysopen = "Monday-Friday";
            }

            if ($facility->user_id == "0") {
                if ($facility->address <> $data[6]) {
                    $facility->address = $data[6];
                }
                if ($data[10] <> "" && $facility->phone <> $data[10]) {
                    $facility->phone = $data[10];
                }
            }

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            $facility->status = $data[3];
            if ($data[3] == "INACTIVE") {
                $facility->approved = -1;
            }

            if (preg_match( "/FAMILY CARE|GROUP CARE/i",$facility->type)) {
                $facility->is_center =  0;
                if (preg_match( "/GROUP CARE/i",$facility->type)) {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            if($data[12] != "") {
            	$contactName = explode(" ", $data[12], 2);
            	$facility->contact_lastname = $contactName[1];
            	$facility->contact_firstname = $contactName[0];
            }
            
            $facility->district_office = 'Nevada Dept of Health and Human Services - Child Care Licensing';
            
            if (preg_match( "/Clark/i",$facility->county)) {
                $facility->do_phone = '(702) 486-3822';
            } else {
                $facility->do_phone = '(775) 684-4463';
            }
            
            $facility->ludate = date('Y-m-d H:i:s');

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[4] <> "" || $data[11] <> "" ) {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[4] <> "") {
                    $facilityDetail->initial_application_date = date('Y-m-d', strtotime($data[4]));
                }

                if ($data[11] <> "") {
                    $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[11]));
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