<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class NorthDakotaCsvParser extends Command
{
    protected $signature = 'custom:parse-north-dakota-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/north-dakota/NorthDakotaChildCare.csv';
    protected $filename = [];
    
    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $status = [
            '01' => 'out of state',
            '02' => 'state',
            '03' => 'Public Approval',
            '04' => 'Reservation and Tribal',
            '05' => 'Air Force Base',
            '06' => 'Exempt',
            '07' => 'Approved Relative',
            '0' => "",
            '1' => 'out of state',
            '2' => 'state',
            '3' => 'Public Approval',
            '4' => 'Reservation and Tribal',
            '5' => 'Air Force Base',
            '6' => 'Exempt',
            '7' => 'Approved Relative'
        ];

        $type = [
            'C' => 'Child Care Center',
            'E' => 'Preschool',
            'F' => 'Family Child Care',
            'G' => 'Group Child Care Home',
            'H' => 'Group Child Care Facility',
            'I' => 'In-home registered provider',
            'K' => 'School Age Care',
            'M' => 'Multiple License',
            'Q' => 'Approved Relative',
            'R' => 'Tribal Registration',
            'S' => 'Self Certification Affidavit',
        	'P' => 'After School Program',
        	'Z' => 'Day Care Center',
        ];

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 3000, ";")) !== false) {
            $row++;

            if ($row == 1 || $data[0]=="07" || $data[4]=="Q") {
                $this->info("skip " . ++$skipCount);
                continue;
            }

            $this->info("test $row = " . $data[3]);
            
            if ($data[4] == "F" || $data[4] == "G" || $data[4] == "I" || $data[4] == "R" ||$data[4] == "S") {
                if ($data[14] <> "") {
                    $address = explode(" ", $data[14], 2);
                    if ($address[1] != "" && preg_match("/BOX/i",$data[14]) == false) {
                        $data[14] = $address[1];
                    }
                }
                
                if ($data[6] <> "") {
                    $address1 = explode(" ", $data[6], 2);
                    if ($address1[1] != "" && preg_match("/BOX/i",$data[6]) == false) {
                        $data[6] = $address1[1];
                    }
                    if ($data[14]=="") {
                        $data[14] = $data[6];
                    }
                }
            }
            
            if ($data[18] =="") {
                $data[18] = $data[13];
            }
            
            if ($data[17] =="" || $data[17] == "0") {
                $data[17] = $data[9];
            }

            $facility = Facility::where('state_id', $data[3])
                                ->where('name', $data[5])
                                ->where('state', $data[8])
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[3])
                                    ->where('name', $data[5])
                                    ->where('state', $data[8])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('state_id', $data[3])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('operation_id', $data[3])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[5])
                                    ->where('address', $data[14])
                                    ->where('city', $data[15])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[13])
                                    ->where('address', $data[14])
                                    ->where('city', $data[15])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[5])
                                    ->where('address', $data[6])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[13])
                                    ->where('address', $data[6])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[5])
                                    ->where('phone', $data[18])
                                    ->where('zip', $data[17])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[5], $data[7], $data[8]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[5];
                $facility->address = $data[14];
                $facility->city = $data[15];
                $facility->state = $data[16];
                $facility->zip = $data[17];
                $facility->phone = $data[18];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[5]);
                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            if ($data[36] == 'Y') {
                $facility->subsidized = 1;
            }

            if ($data[20] <> '') {
                $facility->status = $status[$data[20]];
            } else {
                $facility->status = $status[$data[0]];
            }

            $facility->state_id = $data[3];
            $facility->operation_id = $data[3];
            $facility->type = $type[$data[4]];

            if ($facility->type == "") {
                continue;
            }

            $facility->capacity = $data[19];

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }

            if ($data[4] == "C" || $data[4] == "E" || $data[4] == "H" || $data[4] == "K" ||$data[4] == "M") {
                $facility->is_center =  1;
            } else  {
                $facility->is_center =  0;
                if ($data[4] == "G") {
                    $facility->approved = 2;
                }
            }

            if ($facility->approved == -1) {
                $facility->approved = 1;
            }

            if ($data[37] == "Y") {
                $facility->approved = -1;
                $facility->status = 'Provider Ineligible ' . $data[38];
            }

            if ($data[24] == "1" && preg_match("/QRIS Rating/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "QRIS Rating: " . $data[25] . "; ";
                $facility->state_rating = str_replace("ST", "", $data[25]);
            }

            $facility->district_office = 'North Dakota Dept of Human Services -  Early Childhood Services';
            $facility->do_phone = '(701) 328-2316';

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[10] <> "" && $data[11] && "" || $data[12] <> "") {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if (($data[31]=="11" || $data[31]=="09" || $data[31]=="06" || $data[31]=="04") && $data[32] == "31") {
                    $data[32] = "30";
                }

                if ($data[31]=="02" && $data[32] == "31") {
                    $data[32] = "28";
                }

                if ($data[30] == "0") {
                    continue;
                }

                if (($data[10]=="11" || $data[10]=="09" || $data[10]=="06" || $data[10] == "04") && $data[11] == "31") {
                    $data[11] = "30";
                }

                if ($data[10]=="02" && $data[11] == "31") {
                    $data[11] = "28";
                }

                $facilityDetail->current_license_begin_date = $data[30] . "-" . $data[31] . "-" . $data[32];
                $facilityDetail->current_license_expiration_date = $data[12] . "-" . $data[10] . "-" . $data[11];

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