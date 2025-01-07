<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class MontanaCsvParser extends Command
{
    protected $signature = 'custom:parse-montana-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/montana/MontanaChildCare.csv';
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

            $this->info("test $row = " . $data[0]);

            $facility = Facility::where('state_id', $data[1])
                                ->where('state', 'MT')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[1])
                                    ->where('state', 'MT')
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[2], 'mt');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[0];
                $facility->city = $data[2];
                $facility->state = "MT";
                $facility->zip = $data[3];
                $facility->phone = $data[11];

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

            $facility->state_id = $data[1];
            $facility->operation_id = $data[1];
            $facility->type = $data[4];
            if ($data[5] <> "") {
                $facility->capacity = $data[5];
            }
            $facility->status = $data[6];
            
            $contactName = explode(" ", $data[9], 2);
            $facility->contact_lastname = $contactName[1];
            $facility->contact_firstname = $contactName[0];
            
            if ($facility->phone=="") {
                $facility->phone = $data[11];
            }
            if ($facility->address=="") {
                $facility->address = $data[12];
            }
            if ($facility->state=="") {
                $facility->state = 'MT';
            }
            
            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }
            if ($data[4] == "Child Care Center") {
                $facility->is_center =  1;
            } else  {
                $facility->is_center =  0;
                if ($data[4] == "Group Child Care") {
                    $facility->approved = 2;
                }
            }

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }

            $facility->district_office = 'Montana Dept of Public Health and Human Services - Child Care Licensing Program';
            $facility->do_phone = '406-444-2012';
            $facility->licensor = $data[10];

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[7] <> "" && $data[7] <> "Invalid date" ) {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[7] <> "") {
                    $expDates = explode("/", $data[7], 3);
                    $facilityDetail->current_license_begin_date = $expDates[2] . "-" . $expDates[0] . "-" . $expDates[1];
                }

                if ($data[8] <> "") {
                    $expDates = explode("/", $data[8], 3);
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