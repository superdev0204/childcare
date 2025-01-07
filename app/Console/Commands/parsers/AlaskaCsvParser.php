<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class AlaskaCsvParser extends Command
{
    protected $signature = 'custom:parse-alaska-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/alaska/AlaskaChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $row = 0;
        $existCount = 0;
        $newCount = 0;
        $skipCount = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[0]);

            if ($data[9] == "APPROVED RELATIVE" || $data[9] == "Approved Provider") {
                $address = explode(" ", $data[3], 2);

                if ($address[1]) {
                    $data[3] = $address[1];
                }
            }

            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[7]);

            if (strlen($strippedPhoneNumber) == 10) {
                $oldphone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $strippedPhoneNumber);
            }

            $facility = Facility::where('state_id', trim($data[15]))
                                ->where('state', 'AK')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('contact_firstname', $data[1])
                                    ->where('state', 'AK')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('contact_lastname', $data[2])
                                    ->where('state', 'AK')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[7])
                                    ->first();
            }
            
            if (!$facility && isset($oldphone)) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $oldphone)
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[7])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility && isset($oldphone)) {
                $facility = Facility::where('phone', $oldphone)
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[4], $data[5]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                
                $this->info('new record ' . $newCount);

                $facility = new \stdClass();
                $facility->name = $data[0];
                $facility->address = $data[3];
                $facility->city = $data[4];
                $facility->state = $data[5];
                $facility->zip = $data[6];
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
                
                $this->info("existing $existCount " . $facility->name . " | " . $data[0]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }

            if (isset($facility->approved) && $facility->approved <> 2) {
                $facility->approved = 1;

                if ($data[9] == "GROUP HOME LICENSED (NEW)" || $data[9] == "LICENSED HOME") {
                    $facility->approved = 2;
                }
            }

            $facility->status = $data[8];
            $facility->contact_firstname = $data[1];
            $facility->contact_lastname = $data[2];
            $facility->phone = $data[7];
            $facility->type = $data[9];
            $facility->subsidized = ($data[10]== 'YES') ? 1: 0;
            if ($data[13] <> '' && $data[13] <> 'NOT AVAILABLE') {
                $facility->capacity = $data[13];
            }
            if ($data[14] <> '' && $data[14] <> 'NOT AVAILABLE') {
                $facility->age_range = $data[14];
            }
            $facility->state_id = $data[15];
            
            if ($data[9] == "EXEMPT CENTER" || $data[9] == "LICENSED CENTER" || $data[9] == 'MILITARY CENTER') {
                $facility->is_center = 1;
            } else {
                $facility->is_center = 0;
            }

            if (strcasecmp($facility->city,'Juneau') == 0) {
                $facility->district_office = 'Alaska Division of Public Assistance - Southeast Regional Office';
                $facility->do_phone = '907-465-4756';
            } elseif (strcasecmp($facility->city,'Anchorage') == 0) {
                $facility->district_office = 'Alaska Division of Public Assistance - South Central Regional Office';
                $facility->do_phone = '907-269-4500';
            } elseif (strcasecmp($facility->city,'Fairbanks') == 0) {
                $facility->district_office = 'Alaska Division of Public Assistance - Northern Regional Office';
                $facility->do_phone = '907-451-3198';
            } else {
                $facility->district_office = 'Alaska Division of Public Assistance - Child Care Program Office';
                $facility->do_phone = '1-888-268-4632';
            }

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }
            
            if ($data[11]<> 'EXEMPT' && $data[12]<> 'EXEMPT') {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();
                
                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }
                
                if ($data[11] <> "EXEMPT") {
                    $facilityDetail-> current_license_begin_date = date('Y-m-d', strtotime($data[11]));
                }
                if ($data[12] <> "EXEMPT") {
                    $facilityDetail-> current_license_expiration_date = date('Y-m-d', strtotime($data[12]));
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