<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class WyomingOldCsvParser extends Command
{
    protected $signature = 'custom:parse-wyoming-old-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/wyoming/childcare-requested.csv';
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

            if ($row <= 1) {
                continue;
            }

            $this->info("test $row = " . $data[3]);

            if ($data[11] == "FCCH") {
                $address = explode(" ", $data[4], 2);
                if ($address[1] != "") {
                    $data[4] = $address[1];
                }
            }

            $facility = Facility::where('state_id', trim($data[13]))
                                ->where('state', 'WY')
                                ->first();
            
            if (!$facility && $data[14] <> "") {
                $facility = Facility::where('state_id', trim($data[14]))
                                    ->where('state', 'WY')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', trim($data[3]))
                                    ->where('phone', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[3]))
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[8])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[3], $data[6], 'wy');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = $data[3];
                $facility->address = $data[4];
                $facility->city = $data[6];
                $facility->state = "WY";
                $facility->zip = $data[7];
                $facility->county = $data[0];
                $facility->phone = $data[8];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[3]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if ($data[11] == "CCC") {
                $facility->is_center = 1;
                $facility->type = "Child Care Center";
            } else {
                $facility->is_center = 0;
                if ($data[11] == "FCCC") {
                    $facility->type = "Family Child Care Center";
                    $facility->approved = 2;
                } else {
                    $facility->type = "Family Child Care Home";
                }
            }

            $facility->contact_firstname = $data[1];
            $facility->contact_lastname = $data[2];
            $facility->status = $data[10];
            $facility->capacity = $data[12];
            $facility->state_id = $data[13];

            if ($data[14] <> "") {
                $facility->operation_id = $data[14];
            } else {
                $facility->operation_id = $data[13];
            }

            $facility->daysopen = 'Monday-Friday';
            $facility->district_office =  "Wyoming Dept of Family Services - Early Childhood Programs Division";
            $facility->do_phone =  "(307) 777-5491";

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                DB::table('facility')->insert((array) $facility);
            }

            if ($data[9] <> "" ) {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[9] <> "") {
                    $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[9]));
                    $facilityDetail->mailing_address = $data[5];
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