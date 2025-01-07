<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class MichiganCsvParser extends Command
{
    protected $signature = 'custom:parse-michigan-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/michigan/MichiganChildCare.csv';

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $file = base_path($this->file);
        $handle = fopen($file, 'r');

        $filename = [];

        while (($data = fgetcsv($handle, 2000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[0]);

            if ($data[12] == "Family" || $data[12] == "Group") {
                $address = explode(" ", $data[1], 2);
                if ($address[1] != "") {
                    $data[1] = $address[1];
                }
            }

            $facility = Facility::where('state_id', $data[11])
                                ->where('state', 'MI')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[11])
                                    ->where('state', 'MI')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', trim($data[0]))
                                    ->where('phone', $data[6])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[1])
                                    ->where('zip', $data[4])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('phone', $data[6])
                                    ->where('address', $data[1])
                                    ->where('zip', $data[4])
                                    ->first();
            }

            if (!$facility) {
                $name = preg_replace("/[^A-Za-z0-9]/", '-', $data[0]);
                $city = preg_replace("/[^A-Za-z0-9]/", '-', $data[2]);

                $namecity = $name. "-" . $city;
                $namecity = strtolower($namecity);

                if (!isset($filename[$namecity])) {
                    $filename[$namecity] = 1;
                } else {
                    $filename[$namecity] = $filename[$namecity]+1;
                    $name = $name . $filename[$namecity];
                }

                $uniqueFilename = strtolower($name . "-" . $city . "-mi");
                $uniqueFilename = str_replace(array("---","--"),"-", $uniqueFilename);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                if ($data[7] == "Closed") {
                    $skipCount++;
                    $this->info("\tskip closed record $row = " . $data[0]);
                    continue;
                }
                
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->city = trim($data[2]);
                $facility->state = 'MI';
                $facility->county = $data[5];

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
                if ($data[7] == "Closed") {
                    if ($facility->approved>0) {
                        $facility->approved = -1;
                        $facility->status = $data[7] . " " . $data[26];
                        $facility->ludate = date('Y-m-d H:i:s');
                        $facility->save();
                    }
                    $skipCount++;
                    $this->info("\tskip closed record $row = " . $data[0]);
                    continue;
                } else {
                    if ($facility->approved == -5) {
                        $skipCount++;
                        $facility->ludate = date('Y-m-d H:i:s');
                        $facility->save();
                        continue;
                    }
                    
                    $existCount++;
                    if ($facility->approved == -1) {
                        $facility->approved = 1;
                        $facility->status = $data[7];
                    }
                    $this->info("existing $existCount " . $facility->name . " | " . $data[0]);
                    
                }
            }
            $facility->name = $data[0];
            $facility->address = trim($data[1]);
            $facility->phone = $data[6];
            $facility->zip = substr($data[4],0,5);
            $facility->status = $data[7];
            
            $facility->operation_id = $data[11];
            
            if ((!isset($facility->hoursopen) || $facility->hoursopen == "") && $data[18] != "NOT OPEN") {
                $facility->hoursopen = $data[18];
            }

            if ($data[24] == "YES") {
                $facility->typeofcare = "Full Day Program. ";
            } else {
                $facility->typeofcare = "Half Day Program. ";
            }
            
            if ($data[25] <> "") {
                $facility->typeofcare .= $data[25];
            }
            
            $facility->daysopen = "Monday - Friday";

            if ($data[23]<> "NOT OPEN") {
                $facility->daysopen .= " Saturday";
            }

            if ($data[17]<> "NOT OPEN") {
                $facility->daysopen .= " Sunday";
            }

            if ($data[16] == "School Year" && preg_match("/School Year/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= " Open School Year only;";
            }

            if ($data[12] == "Center") {
                $facility->is_center = 1;
                $facility->type = 'Child Care Center';
            } else {
                $facility->is_center = 0;
                if ($data[12] == "Group Home") {
                    $facility->type = 'Child Care Group Home';
                    $facility->approved = 2;
                } else {
                    $facility->type = 'Child Care Home';
                }
            }

            $facility->state_id = $data[11];
            $facility->capacity = (int) $data[13];

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
                $facilityId = DB::getPdo()->lastInsertId();
                $facility = Facility::where('id', $facilityId)->first();
            }

            $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

            if (!$facilityDetail) {
                $facilityDetail = new \stdClass();
                $facilityDetail->facility_id = $facility->id;
            }
            $facilityDetail->license_holder = $data[8];
            if ($data[14] <> "") {
                $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[14]));
            }

            if ($data[15] <> "") {
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[15]));
            }

            if (isset($facilityDetail->id)) {
                $facilityDetail->save();
            } else {
                DB::table('facilitydetail')->insert((array) $facilityDetail);
            }

            if ($data[18] <> "" || $data[19] <> "") {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }

                if ($data[18] <> "" ) {
                    $facilityHour->monday = $data[18];
                }

                if ($data[19] <> "" ) {
                    $facilityHour->tuesday = $data[19];
                }

                if ($data[20] <> "" ) {
                    $facilityHour->wednesday = $data[20];
                }

                if ($data[21] <> "" ) {
                    $facilityHour->thursday = $data[21];
                }

                if ($data[22] <> "" ) {
                    $facilityHour->friday = $data[22];
                }

                if ($data[23] <> "" ) {
                    $facilityHour->saturday = $data[23];
                }

                if ($data[17] <> "" ) {
                    $facilityHour->sunday = $data[17];
                }

                if (isset($facilityHour->id)) {
                    $facilityHour->save();
                } else {
                    DB::table('facilityhours')->insert((array) $facilityHour);
                }
            }
        }

        $this->info("$existCount exists; $newCount new; $skipCount skipped");

        fclose($handle);
    }
}
