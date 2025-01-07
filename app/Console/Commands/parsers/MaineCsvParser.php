<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class MaineCsvParser extends Command
{
    protected $signature = 'custom:parse-maine-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/maine/MaineChildCare.csv';
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

            #$this->info("test $row = " . $data[0]);

            if ($data[7] == "Family Child Care Provider") {
                $address = explode(" ", $data[1], 2);
                if ($address[1] != "") {
                    $data[1] = $address[1];
                }
            }

            $data[4] = str_pad($data[4], 5, "0", STR_PAD_LEFT);
            $data[6] = str_replace(" ","",$data[6]);
            $data[0] = substr($data[0], 0, 75);

            $this->info("test $row = " . $data[0]);
            
            $facility = Facility::where('state_id', $data[9])
                                ->where('state', 'ME')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('state', $data[3])
                                    ->where('zip', $data[4])
                                    ->where('address', $data[1])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('state', $data[3])
                                    ->where('city', $data[2])
                                    ->where('address', $data[1])
                                    ->first();
            }

            if (!$facility && $data[6] <> "") {
                $facility = Facility::where('name', $data[0])
                                    ->where('state', $data[3])
                                    ->where('zip', $data[4])
                                    ->where('phone', $data[6])
                                    ->first();
            }

            if (!$facility && $data[6] <> "") {
                $facility = Facility::where('name', $data[0])
                                    ->where('state', $data[3])
                                    ->where('city', $data[2])
                                    ->where('phone', $data[6])
                                    ->first();
            }

            if (!$facility && $data[6] <> "") {
                $facility = Facility::where('phone', $data[6])
                                    ->where('state', $data[3])
                                    ->where('city', $data[2])
                                    ->where('address', $data[1])
                                    ->first();
            }

            if (!$facility && $data[6] <> "") {
                $facility = Facility::where('phone', $data[6])
                                    ->where('state', $data[3])
                                    ->where('zip', $data[4])
                                    ->where('address', $data[1])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[2], $data[3]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[0];
                $facility->address = $data[1];
                $facility->city = $data[2];
                $facility->state = $data[3];
                $facility->zip = $data[4];
                $facility->phone = $data[6];

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

            $facility->state_id = $data[9];
            if ($data[8] <> "" && $data[8] <> "N/A") {
                $facility->age_range = $data[8];
            }

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if ($data[7] == "Family Child Care Provider") {
                $facility->is_center =  0;

                $contactName = explode(", ", $data[0], 2);
                $facility->contact_firstname = $contactName[1];
                $facility->contact_lastname = $contactName[0];

            } else {
                $facility->is_center =  1;
            }

            $facility->type = $data[7];
            $facility->status = $data[15];

            if ($data[17] <> "") {
                $facility->capacity = $data[17];
            }
            if ($data[22] == "Yes") {
                $facility->subsidized = 1;
            }

            if ($data[5] <> "0" && preg_match( "/Quality Rating/i",$facility->additionalInfo)==false) {
                $facility->additionalInfo .= "Quality Rating - Step " . $data[5] . "; ";
            }
            if ($data[5] <> "") {
                $facility->state_rating = $data[5];
            }

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }

            $facility->district_office = 'Maine Dept of Health and Human Services - Child Care Licensing';
            $facility->do_phone = '1-207-287-9300';
            $facility->licensor = $data[13];

            if ($data[14] <> "") {
                $facility->licensor .= " (" . $data[14] . ")";
            }

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[20] <> "" || $data[21] <> "") {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[20] <> "") {
                    $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[20]));
                }

                if ($data[21] <> "") {
                    $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[21]));
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