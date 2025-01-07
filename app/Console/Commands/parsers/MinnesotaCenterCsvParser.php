<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;
use App\Models\Counties;

class MinnesotaCenterCsvParser extends Command
{
    protected $signature = 'custom:parse-minnesota-center-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/minnesota/daycarecenter.csv';
    protected $filename = [];
    
    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');
        
        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[2]);

            $data[2] = trim($data[2]);
            $data[8] = substr($data[8],0,5);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'MN')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('operation_id', $data[0])
                                    ->where('state', 'MN')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', trim($data[2]))
                                    ->where('phone', $data[10])
                                    ->where('state', 'MN')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[2])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[10])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[6], $data[7]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }


            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = $data[2];
                $facility->address = $data[3];
                $facility->address2 = $data[4];
                $facility->city = $data[6];
                $facility->state = $data[7];
                $facility->zip = $data[8];
                $facility->county = $data[9];
                $facility->phone = $data[10];

                $facility->is_center = 1;

                $facility->operation_id = $data[0];

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

            }

            $facility->capacity = $data[13];
            $facility->type = $data[1];
            $facility->status = $data[11];
            $facility->state_id = $data[0];
            $facility->age_range = $data[14];
            
            if ($facility->additionalInfo=="" && $data[15] <>'None') {
                $facility->additionalInfo = $data[15];
            }

            if ($data[11] <> 'Active') {
                $facility->approved=-1;
            }

            if ($facility->county <> '') {
                $county = Counties::where('state', $facility->state)
                                ->where('county', $facility->county)
                                ->first();

                if ($county) {
                    $facility->district_office = $county->district_office;
                    $facility->do_phone = $county->do_phone;
                }
            } else {
                $facility->district_office = $data[17];
            }

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[18] <> "" || $data[19] <> "" || $data[20] <> "") {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[18] <> "") {
                    $expDates = explode("/", $data[18], 3);
                    $facilityDetail->initial_application_date = $expDates[2] . "-" . $expDates[0] . "-" . $expDates[1];
                }

                if ($data[19] <> "") {
                    $expDates = explode("/", $data[19], 3);
                    $facilityDetail->current_license_begin_date = $expDates[2] . "-" . $expDates[0] . "-" . $expDates[1];
                }

                if ($data[20] <> "") {
                    $expDates = explode("/", $data[20], 3);
                    $facilityDetail->current_license_expiration_date = $expDates[2] . "-" . $expDates[0] . "-" . $expDates[1];
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