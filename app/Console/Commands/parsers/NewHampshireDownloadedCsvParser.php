<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class NewHampshireDownloadedCsvParser extends Command
{
    protected $signature = 'custom:parse-new-hampshire-downloaded-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/new-hampshire/new-hampshire2.csv';
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
                $this->info("skip " . ++$skipCount);
                continue;
            }

            $this->info("test $row = " . $data[1]);
            
            if ($data[4] == "Family Based Program License") {
                $address = explode(" ", $data[11], 2);
                if ($address[1] != "") {
                    $data[11] = $address[1];
                }
            }
            
            if ($data[16]=="" && $data[10]<>"") {
                $data[16] = $data[10];
            }

            $data[14] = str_pad($data[14],5,"0",STR_PAD_LEFT);
            if (strlen($data[14])> 5) {
                $data[14] = substr($data[14], 0, 5);
            }

            $facility = null;
            if ($data[2]<>"") {
                $facility = Facility::where('state_id', $data[2])
                                    ->where('state', 'NH')
                                    ->first();
                
                if (!$facility) {
                    $facility = Facility::where('operation_id', $data[2])
                                        ->where('state', 'NH')
                                        ->first();
                }
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[11])
                                    ->where('zip', $data[14])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[16])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('address', $data[11])
                                    ->where('phone', $data[16])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[12], $data[13]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[0];
                $facility->address = $data[11];
                $facility->city = $data[12];
                $facility->state = $data[13];
                $facility->zip = $data[14];
                $facility->county = $data[15];
                $facility->phone = $data[16];

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
            $facility->type = $data[4];
            $facility->capacity = $data[19];

            $facility->contact_firstname = $data[17];
            $facility->contact_lastname = $data[18];

            if ($facility->phone == "") {
                $facility->phone = $data[16];
            }

            if (trim($data[20])>0) {
                $facility->age_range = trim($data[20]) . " weeks";
            } else {
                if ($data[22]> 0) {
                    $facility->age_range = trim($data[22]) . " years ";
                }
                if ($data[21]>0) {
                    $facility->age_range .= trim($data[21]) . " months";
                }
            }

            if ($data[23] > 0) {
                $facility->age_range .= " to " . trim($data[23]) . " years ";
            }

            $facility->status = $data[3];

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if ($data[4] == "Family Based Program License") {
                $facility->is_center =  0;
                if ($facility->capacity >= 10) {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }

            $facility->district_office = 'New Hampshire Dept of Health and Human Services - Child Care Licensing Unit';
            $facility->do_phone = '(603) 271-4624';

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[5] <> "" ) {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                $facilityDetail->mailing_address = $data[5];
                $facilityDetail->mailing_city = $data[6];
                $facilityDetail->mailing_state = $data[7];
                $facilityDetail->mailing_zip = $data[8];

                if ($data[26] <> '') {
                    $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[26]));
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