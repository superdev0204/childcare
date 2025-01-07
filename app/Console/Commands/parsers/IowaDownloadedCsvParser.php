<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class IowaDownloadedCsvParser extends Command
{
    protected $signature = 'custom:parse-iowa-downloaded-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/iowa/cc-providers.csv';
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
                continue;
            }
            
            $this->info("test $row = " . $data[4]);
            
            if ($data[2] != "Licensed Center") {
                $address = explode(" ", $data[7], 2);
                if ($address[1] != "") {
                    $data[7] = $address[1];
                }
            }

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'IA')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[3])
                                    ->where('state', 'IA')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[4])
                                    ->where('phone', $data[10])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[4])
                                    ->where('address', $data[7])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[10])
                                    ->where('address', $data[7])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[10])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[4], $data[1], 'ia');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->city = $data[1];
                $facility->state = "IA";
                $facility->zip = $data[9];
                $facility->county = $data[0];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[4]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->name = $data[4];
            $facility->address = $data[7];
            $facility->address2 = $data[8];
            $facility->phone = $data[10];

            $facility->type = $data[2];
            $facility->state_id = $data[3];
            $facility->operation_id = $data[3];

            $facility->contact_lastname = $data[5];
            $facility->contact_firstname = $data[6];
            if ($data[13]<> "") {
                if (strlen($data[13])>5) {
                    $capacity = explode(" ", $data[13], 2);
                    $facility->capacity = $capacity[0];
                } else {
                    $facility->capacity = $data[13];
                }
            }

            if ($data[14] > 0) {
                $facility->state_rating = $data[14];
                if (preg_match("/QRS RATING/i",$facility->additionalInfo) == false) {
                    $facility->additionalInfo .= "QRS RATING: " . $data[14] . " Stars. ";
                }
            }

            if ($data[16] == "Yes") {
                $facility->subsidized = 1;
            }

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if ($data[2] == "Licensed Center") {
                $facility->is_center =  1;
            } else  {
                $facility->is_center =  0;
                if ($facility->capacity >= 10) {
                    $facility->approved = 2;
                }
            }

            $facility->district_office = 'Iowa Department of Human Services - Child Care Assistance Unit';
            $facility->do_phone = '1-866-448-4605';

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[11] <> "" || $data[12] <> "" ) {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[11] <> "") {
                    $expDates = explode("/", $data[11], 3);
                    $facilityDetail->current_license_begin_date = $expDates[2] . "-" . $expDates[0] . "-" . $expDates[1];
                }

                if ($data[12] <> "") {
                    $expDates = explode("/", $data[12], 3);
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