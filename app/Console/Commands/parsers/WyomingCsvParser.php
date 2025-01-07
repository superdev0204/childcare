<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class WyomingCsvParser extends Command
{
    protected $signature = 'custom:parse-wyoming-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/wyoming/WyomingChildCare.csv';
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

            if ($row <= 1) {
                continue;
            }

            $this->info("test $row = " . $data[3]);

            if ($data[1] == "FCCH") {
                $address = explode(" ", $data[3], 2);
                if ($address[1] != "") {
                    $data[3] = $address[1];
                }
            }

            $facility = Facility::where('state_id', trim($data[0]))
                                ->where('state', 'WY')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', trim($data[0]))
                                    ->where('state', 'WY')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[2]))
                                    ->where('phone', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[2]))
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[8])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[4], 'wy');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = $data[2];
                $facility->address = $data[3];
                $facility->city = $data[4];
                $facility->state = "WY";
                $facility->zip = $data[6];
                $facility->county = $data[7];
                $facility->phone = $data[8];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[2]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if ($data[1] == "CCC") {
                $facility->is_center = 1;
                $facility->type = "Child Care Center";
            } else {
                $facility->is_center = 0;
                if ($data[1] == "FCCC") {
                    $facility->type = "Family Child Care Center";
                    $facility->approved = 2;
                } else {
                    $facility->type = "Family Child Care Home";
                }
            }

            if ($data[9] <> "") {
                $facility->email = $data[9];
            }

            if ($data[10] <> "") {
                $facility->website = $data[10];
            }

            $facility->capacity = $data[11];
            $facility->state_id = $data[0];
            $facility->operation_id = $data[0];

            if ($data[30] <> "") {
                $facility->transportation = $data[30];
            }

            if ($data[32] == "Yes") {
                $facility->subsidized = 1;
            }

            if ($data[20] == "Yes" && preg_match("/Infant/",$facility->age_range) == false) {
                $facility->age_range .= "Infants (0-12 months); ";
            }

            if ($data[21] == "Yes" && preg_match("/12-24 months/",$facility->age_range) == false) {
                $facility->age_range .= "Toddler (12-24 months); ";
            }

            if ($data[22] == "Yes" && preg_match("/24-36 months/",$facility->age_range) == false) {
                $facility->age_range .= "Toddler (24-36 months); ";
            }

            if ($data[23] == "Yes" && preg_match("/Pre-School/",$facility->age_range) == false) {
                $facility->age_range .= "Pre-School (3-5 years old); ";
            }

            if ($data[24] == "Yes" && preg_match("/School-Age/",$facility->age_range) == false) {
                $facility->age_range .= "School-Age (6-12 years old); ";
            }

            if ($data[25] == "Yes" && preg_match("/Full Day Care/",$facility->typeofcare) == false) {
                $facility->typeofcare .= "Full Day Care; ";
            }

            if ($data[26] == "Yes" && preg_match("/Half Day Care/",$facility->typeofcare) == false) {
                $facility->typeofcare .= "Half Day Care; ";
            }

            if ($data[27] == "Yes" && preg_match("/Weekend Care/",$facility->typeofcare) == false) {
                $facility->typeofcare .= "Weekend Care; ";
            }

            if ($data[28] == "Yes" && preg_match("/Drop-In Care/",$facility->typeofcare) == false) {
                $facility->typeofcare .= "Drop-In Care; ";
            }

            if ($data[29] == "Yes" && preg_match("/Night Care/",$facility->typeofcare) == false) {
                $facility->typeofcare .= "Night Care; ";
            }

            $facility->district_office =  "Wyoming Dept of Family Services - Early Childhood Programs Division";
            $facility->do_phone =  "(307) 777-5491";

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                DB::table('facility')->insert((array) $facility);
            }

            $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();
            
            if (!$facilityHour) {
                $facilityHour = new \stdClass();
                $facilityHour->facility_id = $facility->id;
            }

            $facilityHour->monday = $data[14];
            $facilityHour->tuesday = $data[15];
            $facilityHour->wednesday = $data[16];
            $facilityHour->thursday = $data[17];
            $facilityHour->friday = $data[18];
            $facilityHour->saturday = $data[19];
            $facilityHour->sunday = $data[13];

            if (isset($facilityHour->id)) {
                $facilityHour->save();
            } else {
                DB::table('facilityhours')->insert((array) $facilityHour);
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