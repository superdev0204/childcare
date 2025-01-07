<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Cities;

class IdahoCsvParser extends Command
{
    protected $signature = 'custom:parse-idaho-csv';
    protected $description = 'Parse data using a specified parser';        
    protected $file = '/datafiles/idaho/IdahoChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 3000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }
			$matchcount = '';
            if ($data[0] == "") {
                $data[0] = ucwords(strtolower($data[1])) . " " . $data[3];
            }

            $this->info("test $row = " . $data[0]);

            if ($data[3] == "Family Daycare Home" && $data[4] <> "") {
                $address = explode(" ", $data[4], 2);
                if ($address[1] != "") {
                    $data[4] = $address[1];
                }
            }

            $data[8] = $data[8] . "-" . $data[9];

            $facility = Facility::where('state_id', $data[16])
                                ->where('state', 'ID')
                                ->first();
            
            $matchcount = "state id";

            if (!$facility) {
                $facility = Facility::where('operation_id', $data[16])
                                    ->where('state', 'ID')
                                    ->first();

                $matchcount = 'op id';
            }

            if (!$facility && $data[4]<> '') {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();

                $matchcount = 'name address zip';
            }

            if (!$facility && $data[4] <> "") {
                $facility = Facility::where('phone', $data[8])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();

                $matchcount = 'phone add zip';
            }

            if (!$facility && $data[4] <> "") {
                $facility = Facility::where('phone', 'LIKE', '%' . $data[9])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();

                $matchcount = 'add zip';
            }

            if (!$facility && $data[4] <> "") {
                $facility = Facility::where('phone', $data[8])
                                    ->where('address', 'LIKE', substr($data[4], 0, 10) . '%')
                                    ->where('zip', $data[7])
                                    ->first();

                $matchcount = 'phone zip';
            }

            if (!$facility && $data[4] <> "") {
                $facility = Facility::where('phone', 'LIKE', '%' . $data[9])
                                    ->where('address', 'LIKE', substr($data[4], 0, 10) . '%')
                                    ->where('zip', $data[7])
                                    ->first();

                $matchcount = 'phone address zip';
            }

            if (!$facility && $data[4] <> "") {
                $facility = Facility::where('address', $data[4])
                                    ->where('city', $data[5])
                                    ->where('state', 'ID')
                                    ->first();

                $matchcount = 'add city';
            }

            if (!$facility && $data[4] <> "") {
                $facility = Facility::where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->where('state', 'ID')
                                    ->first();

                $matchcount = 'add zip';
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[8])
                                    ->first();

                $matchcount = 'phone name';
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', 'LIKE', '%' . $data[9])
                                    ->where('state', 'ID')
                                    ->first();

                $matchcount = 'name phone';
            }

            if (!$facility && $data[4] <> "") {
                $facility = Facility::where('address', 'LIKE', substr($data[4], 0, 10) . '%')
                                    ->where('zip', $data[7])
                                    ->where('state', 'ID')
                                    ->first();

                $matchcount = 'zip phone';
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[5], 'id');

                $facility = Facility::where('filename', $uniqueFilename)->first();

                $matchcount = 'filename';
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[0];
                $facility->address = $data[4];
                $facility->city = $data[5];
                $facility->state = 'ID';
                $facility->zip = $data[7];

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
                $this->info("existing $existCount " . $matchcount . "|" . $facility->name . " | " . $data[0]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->state_id = $data[6];
            $facility->operation_id = $data[6];
            $facility->contact_lastname = $data[1];
            $facility->contact_firstname = $data[2];
            $facility->type = $data[3];
            $facility->phone = $data[8];

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if ($data[3] == "Child Care Center" ) {
                $facility->is_center =  1;
            } else  {
                $facility->is_center =  0;
                if ($data[3] == "(FCC)Group Child Care" ) {
                    $facility->approved = 2;
                }
            }

            $facility->age_range = $data[10];
            if (preg_match("/ICCP/", $data[11])) {
                $facility->subsidized = 1;
            }

            $facility->website = $data[16];

            if ($data[17] == "Both") {
                $facility->typeofcare="Full-Time, Part-Time";
            } else {
                $facility->typeofcare=$data[17];
            }

            $facility->accreditation = $data[19];
            $facility->schools_served = $data[21];
            $data[23] = str_replace("English, ","",$data[23]);
            $data[23] = str_replace("English","",$data[23]);
            $facility->language = $data[23];
            $facility->pricing = $data[25];

            if ($data[12] <> "" && preg_match("/Financial Assistance:/",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Financial Assistance: " . $data[12] . ". ";
            }

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }

            $facility->district_office = 'Idaho Dept of Health and Welfare - Daycare Licensing Program';
            $facility->do_phone = '1-800-926-2588';

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                DB::table('facility')->insert((array) $facility);
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