<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class HawaiiCsvParser extends Command
{
    protected $signature = 'custom:parse-hawaii-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/hawaii/hawaii-new.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $type = [
            'BAS' => 'Before & After School Child Care Facility',
            'GCC' => 'Group Child Care Center',
            'GCH' => 'Group Child Care Home',
            'FCC' => 'Family Child Care Home',
            'IT' => 'Infant & Toddler Child Care Center'
        ];

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');
        
        while (($data = fgetcsv($handle,2000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                $this->info("skip " . ++$skipCount);
                continue;
            }

            $this->info("test $row = " . $data[17]);

            if ($data[17]=="") {
                $data[17] = $data[1];
            }
            if ($data[3] =="") {
                $data[3] = $data[9];
            }
            
            if ($data[5] =="") {
                $data[5] = $data[11];
            }
            if ($data[5] =="") {
                $data[5] = $data[23];
            }
            if ($data[6] =="") {
                $data[6] = $data[12];
            }
            
            if ($data[6] == "") {
                $data[6] = "HI";
            }
            
            if ($data[7] =="") {
                $data[7] = $data[13];
            }

            $facility = Facility::where('state_id', trim($data[16]))
                                ->where('state', 'HI')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', trim($data[16]))
                                    ->where('state', 'HI')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[17]))
                                    ->where('address', $data[3])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[15])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[15])
                                    ->where('address', $data[3])
                                    ->where('city', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[17]))
                                    ->where('phone', $data[15])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[17], $data[5], $data[6]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                if ($data[5]=="" && $data[7]=="") {
                    continue;
                }

                $facility = new \stdClass();
                
                $facility->city = $data[5];
                $facility->state = $data[6];
                $facility->county = $data[22];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[17]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }
            if ($facility->approved <= 0) {
                $facility->approved = 1;
            }

            $facility->name = $data[17];
            $facility->address = $data[3];
            $facility->address2 = $data[4] . " " . $data[2];
            $facility->zip = $data[7];
            $facility->phone = $data[15];
            
            $facility->state_id = $data[16];
            $facility->operation_id = $data[16];
            $facility->type = $type[$data[0]];

            $facility->age_range = $data[19] . " to " . $data[20];

            if ($data[14] != "<NO NAME>" && $data[14] != "") {
                $contactName = explode(" ", $data[14], 2);
                $facility->contact_firstname = $contactName[0];
                $facility->contact_lastname = $contactName[1];
            }

            $facility->capacity = $data[18];

            if ($data[0] == "GCH" || $data[0] == "FCC") {
                $facility->is_center =  0;
                if ($data[0] == "GCH") {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }
            if ($facility->county == "HONOLULU") {
                $facility->district_office = 'Oahu Child Care Licensing';
                $facility->do_phone = '(808) 587-5266';
            } elseif ($facility->county == "HAWAII") {
                $facility->district_office = 'Central Hilo Child Care Licensing Unit';
                $facility->do_phone = '(808) 981-7290';
            } elseif ($facility->county == "MAUI") {
                $facility->district_office = 'Central Maui Child Care Licensing Unit';
                $facility->do_phone = '(808) 243-5866';
            } elseif ($facility->county == "KAUAI") {
                $facility->district_office = 'South Child Care Licensing Unit';
                $facility->do_phone = '(808) 241-3660';
            } else {
                $facility->district_office = 'Department of Human Services’ Child Care Licensing';
                $facility->do_phone = '(808) 832-5300';
            }

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

            if (!$facilityDetail) {
                $facilityDetail = new \stdClass();
                $facilityDetail->facility_id = $facility->id;
            }

            /* if ($data[21] <> "") {
                $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[21]));
            } */

            if ($data[21] <> "") {
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[21]));
            }

            if ($data[9] <> "") {
                $facilityDetail->mailing_address = $data[9];
            }

            if ($data[10] <> "") {
                $facilityDetail->mailing_address2 = $data[10];
            }

            if ($data[11] <> "") {
                $facilityDetail->mailing_city = $data[11];
            }

            if ($data[12] <> "") {
                $facilityDetail->mailing_state = $data[12];
            }

            if ($data[13] <> "") {
                $facilityDetail->mailing_zip = $data[13];
            }
            $facilityDetail->license_holder = $data[1];

            if (isset($facilityDetail->id)) {
                $facilityDetail->save();
            } else {
                DB::table('facilitydetail')->insert((array) $facilityDetail);
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