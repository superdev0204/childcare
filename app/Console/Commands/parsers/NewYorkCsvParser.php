<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class NewYorkCsvParser extends Command
{
    protected $signature = 'custom:parse-new-york-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/new-york/NYChildCare.csv';
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

            if (preg_match("/,/", $data[4])) {
                $address = explode(", ", $data[4], 2);
                if ($address[1] != "") {
                    $data[5] = $address[1];
                    $data[4] = $address[0];
                }
            }

            if ($data[4] == "" && $data[5] <> "") {
                $data[4] = $data[5];
            } else {
                if (substr_count($data[4],",") > 1) {
                    $address = explode(",", $data[4], 3);
                    $data[4] = $address[0] . "," . $address[1];
                    $data[5] = trim($address[2]);
                }
                if ($data[1] == "Family Day Care") {
                    $address = explode(" ", $data[4], 2);
                    if ($address[1] != "") {
                        $data[4] = $address[1];
                    }
                }
            }
            
            $data[8] = substr($data[8],0,5);
            $data[2] = substr($data[2], 0,75);

            $this->info("test $row = " . $data[0]);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'NY')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('operation_id', $data[0])
                                    ->where('state', 'NY')
                                    ->first();
            }
            
            if (!$facility && $data[4] <> "") {
                $facility = Facility::where('name', $data[2])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[8])
                                    ->first();
            }

            if (!$facility && $data[4] <> "") {
                $facility = Facility::where('phone', $data[10])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[8])
                                    ->first();
            }

            if (!$facility && $data[9] <> "") {
                $facility = Facility::where('name', $data[2])
                                    ->where('phone', $data[9])
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
                $facility->address = $data[4];
                $facility->address2 = $data[5];
                $facility->city = $data[6];
                $facility->state = $data[7];
                $facility->zip = $data[8];
                $facility->phone = $data[9];

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }

                $facility->filename = $uniqueFilename;

                if (trim($data[16]) <> "Open") {
                    $skipCount++;
                    continue;
                }
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[2]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->state_id = $data[0];
            $facility->operation_id = $data[0];

            if ($facility->address == "") {
                $facility->address = $data[4];
            }

            if ($facility->address2 == "") {
                $facility->address2 = $data[5];
            }

            if($data[11] <> "" && $data[11] <> "There is no approved Director for this site." &&
                $data[11] <> "There is no approved On-Site Provider for this") {
                $contactName = explode(" ", $data[11], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }

            $facility->schools_served = $data[13] . " School District";
            if ($data[14] <> "") {
                $facility->capacity = $data[14];
            }
            $facility->age_range = $data[15];
            $facility->status = $data[16];
            $facility->type = $data[1];

            if (trim($data[16]) <> "Open" || $data[25] =='Yes') {
                $facility->approved = -1;
            } elseif ($data[1] == "Group Family Day Care") {
                $facility->approved = 2;
            } elseif ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if ($data[1] == "Family Day Care" || $data[1] == "Group Family Day Care") {
                $facility->is_center = 0;
            } else {
                $facility->is_center = 1;
            }

            if ($data[1] == "School Age Child Care") {
                $facility->is_afterschool = 1;
            }
            $facility->district_office = $data[26];
            $facility->do_phone = $data[27];

            if ($data[18] == "over-the-counter topical" && preg_match("/over-the-counter topical ointments/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "This facility is authorized to administer over-the-counter topical ointments only; ";
            }

            if ($data[18] == "medications" && preg_match("/administer medications/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "This facility is authorized to administer medications; ";
            }

            if ($data[19] == "Yes" && preg_match("/There has been enforcement actions since 2003/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "There has been enforcement actions since 2003; ";
            }

            if ($data[20] == "Yes" && preg_match("/Care is available during non-traditional hours/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Care is available during non-traditional hours; ";
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

            if ($data[10] <> "") {
                $facilityDetail->fax = $data[10];
            }

            if ($data[17] <> "") {
                $facilityDetail->initial_application_date = date('Y-m-d', strtotime($data[17]));
            }

            if ($data[23] <> "") {
                $dateperiod = explode(" - ", $data[23]);
                $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($dateperiod[0]));
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($dateperiod[1]));
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