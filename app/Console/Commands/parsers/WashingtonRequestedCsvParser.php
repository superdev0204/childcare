<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class WashingtonRequestedCsvParser extends Command
{
    protected $signature = 'custom:parse-washington-requested-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/washington-state/washington-childcare2.csv';
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
                continue;
            }
            
            $this->info("test $row = " . $data[0]);
            $facility = null;
            
            if ($data[0] <> '') {
                $facility = Facility::where('state_id', $data[0])
                                    ->where('state', 'WA')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[2])
                                    ->where('state', 'WA')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('operation_id', $data[0])
                                    ->where('state', 'WA')
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[8], $data[31], $data[32]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = $data[8];

                if($data[10] <> '' && preg_match( "/Unknown/i",$data[10]) ==false) {
                    $contactName = explode(" ", $data[10], 2);
                    $facility->contact_lastname = $contactName[1];
                    $facility->contact_firstname = $contactName[0];
                }

                if ($data[28] <> "") {
                    $facility->address = $data[28];
                }

                $facility->address2 = $data[29];
                $facility->city = $data[31];
                $facility->state = $data[32];
                $facility->zip = substr($data[33],0,5);
                $facility->county = $data[46];
                $facility->phone = $data[15];
                $facility->approved = 1;

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[8]);

                if ($facility->approved == -5) {
                    continue;
                }
            }
            $facility->name = $data[8];
            $facility->capacity = $data[18];
            $facility->status = $data[7];
            $facility->state_id = $data[0];
            $facility->operation_id = $data[2];
            $facility->age_range = $data[19];

            if (preg_match("/Do not refer/i",$data[53])) {
                $facility->approved = -1;
                $facility->status = $data[53];
            } elseif ($facility->approved < 0) {
                $facility->approved = 1;
            }

            if ($data[5] == "Home") {
                $facility->is_center = 0;
                $facility->type = "Family Home Provider";
                if ($facility->capacity >= 10) {
                    $facility->approved = 2;
                }
            } else {
                $facility->is_center = 1;
                if ($data[5] == 'School') {
                    $facility->type = "After School /School Age Program";
                } else {
                    $facility->type = "Child Care Center";
                }
            }

            if ($data[11] <> "" && preg_match("/Initial License Date/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Initial License Date: " . $data[11] . ".  ";
            }

            if ($data[52] <> "Not Participating" && preg_match("/Early Achievers Status/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Early Achievers Status: " . $data[52] . ".  ";
            }

            if(preg_match( "/None/i",$data[16]) == false) {
                $facility->email = $data[16];
            }

            $facility->district_office =  $data[48];
            $facility->licensor =  $data[47];

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

            if ($data[11] <> "") {
                $facilityDetail->initial_application_date = date('Y-m-d', strtotime($data[11]));
            }

            if ($data[12] <> "") {
                $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[12]));
            }

            if ($data[14] <> "") {
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[14]));
            } elseif ($facilityDetail->current_license_expiration_date <> "") {
                $facilityDetail->current_license_expiration_date = "2999-12-31";
            }

            if ($data[17] <> "") {
                $facilityDetail->fax = $data[17];
            }

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