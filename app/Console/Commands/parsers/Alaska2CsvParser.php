<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class Alaska2CsvParser extends Command
{
    protected $signature = 'custom:parse-alaska2-csv';
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

            $this->info('test $row = ' . $data[0]);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'AK')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('operation_id', $data[8])
                                    ->where('state', 'AK')
                                    ->first();
            }

            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[6]);

            if (strlen($strippedPhoneNumber) == 10) {
                $oldphone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $strippedPhoneNumber);
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[6])
                                    ->first();
            }

            if (!$facility && isset($oldphone)) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $oldphone)
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[6])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility && isset($oldphone)) {
                $facility = Facility::where('phone', $oldphone)
                                    ->where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[3], 'ak');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;

                $this->info('new record ' . $newCount);

                $facility = new \stdClass();
                $facility->name = $data[1];
                $facility->address = $data[2];
                $facility->city = $data[3];
                $facility->state = 'AK';
                $facility->zip = $data[5];
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

            $facility->state_id = trim($data[0]);
            $facility->operation_id = trim($data[8]);
            $facility->is_center = $data[10] == 'Licensed Center' ? 1 : 0;
            $facility->approved = $facility->is_center ? 1 : 2;
            $facility->subsidized = strpos($data[23], 'Child Care Assistance') !== false ? 1: 0;
            $facility->accreditation = $data[21];
            $facility->typeofcare = $data[9];
            $facility->phone = $data[6];
            $facility->age_range = $data[7];
            $facility->type = $data[10];
            
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

            $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

            if (!$facilityHour) {
                $facilityHour = new \stdClass();
                $facilityHour->facility_id = $facility->id;
            }

            $facilityHour->monday = $data[11];
            $facilityHour->tuesday = $data[12];
            $facilityHour->wednesday = $data[13];
            $facilityHour->thursday = $data[14];
            $facilityHour->friday = $data[15];
            $facilityHour->saturday = $data[16];
            $facilityHour->sunday = $data[17];

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