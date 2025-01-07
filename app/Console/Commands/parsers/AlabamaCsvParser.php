<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class AlabamaCsvParser extends Command
{
    protected $signature = 'custom:parse-alabama-csv';
    protected $description = 'Parse data using a specified parser';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;
        
        $filename = base_path('/datafiles/alabama/AlabamaChildCare.csv');
        $handle = fopen($filename, 'r');

        if ($handle === false) {
            $this->error("Unable to open the data file '{$filename}'");
            return;
        }

        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            $row++;
            if ($row == 1) {
                continue;
            }

            $data[13] = substr($data[13],0,5);

            $this->info("test $row = " . $data[0]);

            $isCenter = $data[19] == 'Day Care Centers' ? 1 : 0;

            $facility = Facility::where('state_id', $data[2])
                                ->where('name', $data[0])
                                ->where('state', 'AL')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('is_center', $isCenter)
                                    ->where('name', $data[0])
                                    ->where('address', $data[10])
                                    ->where('zip', $data[13])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('is_center', $isCenter)
                                    ->where('name', $data[0])
                                    ->where('address', $data[10])
                                    ->where('city', $data[11])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('is_center', $isCenter)
                                    ->where('phone', $data[1])
                                    ->where('address', $data[10])
                                    ->where('zip', $data[13])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('is_center', $isCenter)
                                    ->where('phone', $data[1])
                                    ->where('address', $data[10])
                                    ->where('city', $data[11])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('is_center', $isCenter)
                                    ->where('name', $data[0])
                                    ->where('phone', $data[1])
                                    ->where('zip', $data[13])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('is_center', $isCenter)
                                    ->where('name', $data[0])
                                    ->where('phone', $data[1])
                                    ->where('city', $data[11])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('is_center', $isCenter)
                                    ->where('address', $data[10])
                                    ->where('zip', $data[13])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('is_center', $isCenter)
                                    ->where('name', $data[0])
                                    ->where('phone', $data[1])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[11], 'al');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                
                $facility->city = trim($data[11]);
                $facility->county = $data[25];
                $facility->state = $data[12];
                                
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
            $facility->name = $data[0];
            $facility->address = $data[10];
            $facility->zip = $data[13];
            $facility->phone = $data[1];
            
            $facility->state_id = $data[2];

            if ($facility->user_id == 0) {
                if ($data[3] == "Licensed") {
                    $facility->operation_id = "At Center";
                } else {
                    $facility->operation_id = "";
                }
            }

            $facility->status = $data[3];
            $facility->type = $data[19];

            if($data[4] != "") {
                $data[4] = str_replace(" - Director","",$data[4]);
                $contactName = explode(" ", $data[4], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }

            if ($data[5] <> "N/A - N/A") {
                $facility->hoursopen = $data[5];
            }

            if ($data[6] <> "N/A - N/A" && preg_match("/Nighttime Care Available/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo = "Nighttime Care Available; " . $facility->additionalInfo;
            }

            if ($data[7] <> " - N/A") {
                $facility->age_range = $data[7];
            }

            $facility->is_center = $isCenter;
            $facility->approved = $data[19] == 'Group Child Care Homes' ? 2 : 1;

            if ($data[20] <> '') {
                $facility->state_rating = $data[20];
            }

            if ($data[22] <> '') {
                $facility->accreditation = $data[22];
            }

            $facility->district_office = "Alabama Department of Human Resources - Child Care Services Division";
            $facility->do_phone = "(334)242-1425";

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                DB::table('facility')->insert((array) $facility);
            }

            if ($data[9] <> "" ) {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();
                
                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                $facilityDetail->mailing_address = $data[9];
                $facilityDetail->mailing_state = $data[15];
                $facilityDetail->mailing_city = $data[16];
                $facilityDetail->mailing_zip = $data[17];
                $facilityDetail->license_holder = $data[14];
                
                if (isset($facilityDetail->id)) {
                    $facilityDetail->save();
                } else {
                    DB::table('facilitydetail')->insert((array) $facilityDetail);
                }
            }

            preg_match('/(\d{1,2}:\d{2}\s*(?:am|pm))\s*-\s*(\d{1,2}:\d{2}\s*(?:am|pm))/i', $data[5], $matches1);
            preg_match('/(\d{1,2}:\d{2}\s*(?:am|pm))\s*-\s*(\d{1,2}:\d{2}\s*(?:am|pm))/i', $data[6], $matches2);

            if ($matches1 || $matches2) {                
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }

                if (empty($matches2)) {
                    $hours = $data[5];
                } elseif (empty($matches1)) {
                    $hours = $data[6];
                } else {
                    $dayTimeHoursStart = strtoupper($matches1[1]);
                    $dayTimeHoursEnd = strtoupper($matches1[2]);
                    $nightTimeHoursStart = strtoupper($matches2[1]);
                    $nightTimeHoursEnd = strtoupper($matches2[2]);

                    if (str_replace(' ', '', $dayTimeHoursEnd) == str_replace(' ', '', $nightTimeHoursStart)) {
                        $hours = $dayTimeHoursStart . ' - ' . $nightTimeHoursEnd;
                    } else {
                        $hours = $data[5] . '; ' . $data[6];
                    }
                }

                $facilityHour->monday = $hours;
                $facilityHour->tuesday = $hours;
                $facilityHour->wednesday = $hours;
                $facilityHour->thursday = $hours;
                $facilityHour->friday = $hours;

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
