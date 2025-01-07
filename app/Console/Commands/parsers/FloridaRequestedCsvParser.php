<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class FloridaRequestedCsvParser extends Command
{
    protected $signature = 'custom:parse-florida-requested-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/florida/fl-master.csv';
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
                $this->info("skip " . ++$skipCount);
                continue;
            }

            $this->info("test $row = " . $data[4]);
            
            if ($data[11] == "Licensed Family Day Care Home" || $data[11] == "Registered Family Day Care Home") {
                $address = explode(" ", $data[5], 2);
                if ($address[1] != "") {
                    $data[5] = $address[1];
                }
            }
            
            if ($data[9] <> "") {
                $data[10] = $data[9] . "-" . $data[10];
            }

            $facility = Facility::where('state_id', $data[3])
                                ->where('state', 'FL')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[3])
                                    ->where('state', 'FL')
                                    ->first();
            }

            if (!$facility && $data[8] <> "") {
                $facility = Facility::where('name', $data[4])
                                    ->where('address', $data[5])
                                    ->where('zip', $data[8])
                                    ->first();
            }

            if (!$facility && $data[10] <> "") {
                $facility = Facility::where('name', trim($data[4]))
                                    ->where('phone', $data[10])
                                    ->first();
            }

            if (!$facility && $data[5] <> "") {
                $facility = Facility::where('address', $data[5])
                                    ->where('phone', $data[10])
                                    ->first();
            }

            if (!$facility && $data[8] <> "") {
                $facility = Facility::where('is_center', 1)
                                    ->where('address', $data[5])
                                    ->where('zip', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[4], $data[6], $data[7]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                if ($data[8] =="" && $data[10]=="") {
                    $skipCount++;
                    continue;
                }
                if ($data[8] =="" && $data[6]=="") {
                    $skipCount++;
                    continue;
                }

                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[4];
                $facility->address = $data[5];
                $facility->city = $data[6];
                $facility->state = 'FL';
                $facility->zip = $data[8];
                $facility->county = $data[2];
                $facility->phone = $data[10];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[4]);
                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->state_id = $data[3];
            $facility->operation_id = $data[3];
            $facility->type = $data[11];

            if ($data[12] <> "") {
                $contactName = explode(" ", $data[12], 2);
                $facility->contact_firstname = $contactName[0];
                $facility->contact_lastname = $contactName[1];
            }

            $facility->accreditation = $data[13];
            $facility->capacity = $data[14];

            if ($data[16] <> "") {
                $facility->email = $data[16];
            }

            if ($data[11] == "Licensed Family Day Care Home" || $data[11] == "Registered Family Day Care Home" || $data[11] == "Large Family Child Care Home") {
                $facility->is_center =  0;
                if ($data[11] == "Licensed Family Day Care Home" || $data[11] == "Large Family Child Care Home") {
                    $facility->approved = 2;
                }
            } else  {
                if ($data[11] == "Facility") {
                    $facility->type = "Child Care Facility";
                }
                $facility->is_center =  1;
            }

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }

            $facility->district_office = 'Florida Dept of Children and Families (DCF)-  Child Care Services';
            $facility->do_phone = '(850) 488-4900';
            $facility->licensor = $data[22];

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[15] <> "" ) {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                $dates = explode("/", $data[15], 3);
                $facilityDetail->initial_application_date = date('Y-m-d', strtotime($data[15]));
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[17]));

                if ($data[18] <> "") {
                    $facilityDetail->mailing_address = $data[18];
                }

                if ($data[19] <> "") {
                    $facilityDetail->mailing_city = $data[19];
                }

                if ($data[20] <> "") {
                    $facilityDetail->mailing_state = $data[20];
                }

                if ($data[21] <> "") {
                    $facilityDetail->mailing_zip = $data[21];
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