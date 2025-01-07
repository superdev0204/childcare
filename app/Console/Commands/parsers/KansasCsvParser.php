<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class KansasCsvParser extends Command
{
    protected $signature = 'custom:parse-kansas-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/kansas/KansasChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $newFile = fopen(base_path("/datafiles/kansas/newnoaddress.csv"), "w");

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 4000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                fwrite($newFile, join($data,";") . "\n");
                $this->info("skip " . ++$skipCount);
                continue;
            }

            $this->info("test $row = " . $data[0]);
            
            if (($data[3] == "Licensed Day Care Home" || $data[3] == "Group Day Care Home") && $data[4] <> "") {
                $address = explode(" ", $data[4], 2);
                if ($address[1] != "") {
                    $data[4] = $address[1];
                }
            }
            
            if (strlen($data[14]) > 15 || $data[14] == "5E+018") {
                $data[14] = str_replace("https://kscapportalp.dcf.ks.gov/OIDS/Comp_details.aspx?fid=","",$data[13]);
            }
            
            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[11]);
            
            if(strlen($strippedPhoneNumber) == 10) {
                $strippedPhoneNumber = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $strippedPhoneNumber);
            }

            $facility = Facility::where('operation_id', $data[1])
                                ->where('state', 'KS')
                                ->first();

            if (!$facility ) {
                $facility = Facility::where('state_id', $data[14])
                                    ->where('state', 'KS')
                                    ->first();
            }

            if (!$facility && $data[4] <> "") {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility && $data[4] <> "") {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[4])
                                    ->where('city', $data[6])
                                    ->first();
            }

            if (!$facility && $data[11] <> "") {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[11])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility && $data[4] <> "" && $data[11] <> "") {
                $facility = Facility::where('phone', $data[11])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility && $data[4] <> "" && $data[11] <> "") {
                $facility = Facility::where('phone', $strippedPhoneNumber)
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility && $data[11] <> "") {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[11])
                                    ->first();
            }

            if (!$facility && $data[11] <> "") {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $strippedPhoneNumber)
                                    ->first();
            }

            if (!$facility && $data[4] <> "" && $data[11] <> "") {
                $facility = Facility::where('address', $data[4])
                                    ->where('phone', $data[11])
                                    ->first();
            }

            if (!$facility && $data[4] <> "" && $data[11] <> "") {
                $facility = Facility::where('address', $data[4])
                                    ->where('phone', $strippedPhoneNumber)
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[6], 'ks');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[0];
                $facility->address = $data[4];
                $facility->city = $data[6];
                $facility->state = "KS";
                $facility->zip = $data[7];
                $facility->county = $data[5];
                $facility->phone = $data[11];
                if (isset($data[16]) && $data[16] <> "") {
                    $facility->website = $data[16];
                }

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }

                $facility->filename = $uniqueFilename;

                if ($data[4] == '' && $data[11]=="" && (!isset($data[15]) || $data[15]=="")) {
                    fwrite($newFile, join($data,";") . "\n");
                }

            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[0]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }

                if ($facility->address == '') {
                    if ($data[4] == '' && $data[11]=="" && (!isset($data[15]) || $data[15]=="")) {
                        fwrite($newFile, join($data,";") . "\n");
                    }
                }
            }

            $facility->state_id = $data[14];
            $facility->operation_id = $data[1];
            $facility->type = $data[3];

            if ($data[12]<> "") {
                $facility->capacity = $data[12];
            }

            if ($data[3] == "Licensed Day Care Home" || $data[3] == "Group Day Care Home") {
                $facility->is_center =  0;
                if ($data[3] == "Group Day Care Home") {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            $facility->status = $data[10];
            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if (($data[10] != "Open" && $data[10] != "Pending") || ($data[11]=="" && $facility->phone == "")) {
                #print "phone is null " . $facility->phone . "\n";
                $facility->approved = -1;
            }

            if ($data[2] <> "" && $facility->is_center==0) {
                $contactName = explode(" ", $data[2], 2);
                $facility->contact_firstname = $contactName[0];
                $facility->contact_lastname = $contactName[1];
            }

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }

            if (isset($data[16]) && $data[16]<>"") {
                $facility->website = $data[16];
            }

            if (isset($data[17]) && $data[17]<>"" && $facility->introduction=="") {
                $facility->introduction = $data[17];
            }

            if (isset($data[18]) && $data[18]<>"") {
                $facility->hoursopen = $data[18];
            }

            if (isset($data[19]) && $data[19]<>"") {
                $facility->logo = $data[19];
            }

            if (isset($data[20]) && $data[20]<>"" && $facility->email=="") {
                $facility->email = $data[20];
            }

            $facility->district_office = 'Kansas Dept of Health and Environment - Child Care Licensing Program';
            $facility->do_phone = '785-296-1270';

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[8] <> "" || $data[9] <> "" ) {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[10] <> "") {
                    $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[10]));
                    $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[11]));
                }

                if ($data[8] <> "") {
                    $expDates = explode("/", $data[8], 3);
                    $facilityDetail->current_license_begin_date = $expDates[2] . "-" . $expDates[0] . "-" . $expDates[1];
                }
                if ($data[9] <> "") {
                    $expDates = explode("/", $data[9], 3);
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

        fclose($handle());
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