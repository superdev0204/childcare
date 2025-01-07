<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;
use App\Models\Counties;

class PennsylvaniaRequestedCsvParser extends Command
{
    protected $signature = 'custom:parse-pennsylvania-requested-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/pennsylvania/pa-childcare2.csv';
    protected $filename = [];

    public function handle()
    {
        $existCount = 0;
        $newCount = 0;
        $skipCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');
        
        while (($data = fgetcsv($handle, 2000, ";")) !== false) {
            $row++;
            
            if ($row == 1) {
                continue;
            }

            if ($data[9] == "Family Child Care Home") {
                $address = explode(" ", $data[11], 2);
                
                if ($address[1]) {
                    $data[11] = $address[1];
                }
            }

            $this->info("test {$row} = {$data[18]}");

            $facility = Facility::where('state_id', $data[18])
                                ->where('state', 'PA')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[18])
                                    ->where('state', 'PA')
                                    ->first();
            }

            if (!$facility && $data[16] <> "") {
                $facility = Facility::where('name', $data[8])
                                    ->where('phone', $data[16])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[8])
                                    ->where('address', $data[11])
                                    ->where('zip', $data[15])
                                    ->first();
            }

            if (!$facility && $data[16] <> "") {
                $facility = Facility::where('phone', $data[16])
                                    ->where('address', $data[11])
                                    ->where('zip', $data[15])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('address', $data[11])
                                    ->where('zip', $data[15])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[8], $data[13], $data[14]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info('new record ' . $newCount);

                $facility = new \stdClass();
                
                $facility->city = $data[13];
                $facility->state = $data[14];
                $facility->county = $data[0];
                $facility->filename = $uniqueFilename;

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[8]);
                
                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }
            $facility->name = $data[8];
            $facility->address = $data[11];
            $facility->address2 = $data[12];
            $facility->zip = $data[15];
            $facility->phone = $data[16];
            
            if ($facility->approved <= 0) {
                $facility->approved = 1;
            }

            if ($data[9] == "Group Child Care Home") {
                $facility->approved = 2;
            }

            if ($data[9] == "Family Child Care Home" || $data[9] == "Group Child Care Home") {
                $facility->is_center = 0;
            } else {
                $facility->is_center = 1;
            }

            $facility->type = $data[9];

            if ($data[16] <> "") {
                $facility->phone = $data[16];
            }

            $facility->state_id = $data[18];
            $facility->operation_id = $data[18];
            $facility->capacity = $data[21];

            if ($facility->county <> '') {
                $county = Counties::where('state', $facility->state)
                                ->where('county', $facility->county)
                                ->first();

                if ($county) {
                    $facility->district_office = $county->district_office;
                    $facility->do_phone = $county->do_phone;
                }
            }

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

            if ($data[19] <> "" ) {
                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[19]));
                $facilityDetail->mailing_address = $data[3];
                $facilityDetail->mailing_city = $data[5];
                $facilityDetail->mailing_state = $data[6];
                $facilityDetail->mailing_zip = $data[7];
                
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