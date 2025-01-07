<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class UtahCsvParser extends Command
{
    protected $signature = 'custom:parse-utah-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/utah/UtahChildCare.csv';
    protected $filename = [];
    
    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $row++;

            if ($row == 1) {
                continue;
            }

            if (strlen($data[0]) > 75) {
                $data[0] = substr($data[0], 0, 75);
            }
            
            $this->info("test $row = " . $data[0]);

            $facility = Facility::where('state_id', $data[12])
                                ->where('state', 'UT')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[12])
                                    ->where('state', 'UT')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[2])
                                    ->first();
            }

            if (!$facility && $data[5] <> "") {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[5])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility && $data[5] <> "") {
                $facility = Facility::where('phone', $data[2])
                                    ->where('address', $data[5])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[6], 'ut');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[0];
                $facility->address = $data[5];
                $facility->city = $data[6];
                $facility->state = "UT";
                $facility->zip = $data[7];
                $facility->phone = $data[2];
                $facility->county = $data[8];

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }

                $facility->filename = $uniqueFilename;
                $facility->approved = 1;

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

            if ($data[1] == "Licensed Family") {
                $facility->approved = 2;
            }

            if ($data[1] == "Exempt Home" || $data[1] == "Licensed Family" ||
                $data[1] == "Alternative Care, Background Screenings by CCL" ||
            	$data[1] == "Exempt Home, No Background Checks by CCL" ||
            	$data[1] == "Residential Certificate" ||
                $data[1] == "Alternative Care, Background Checks by CCL" ||
                $data[1] == 'DWS Approved, FFN') {
                $facility->is_center = 0;
                $facility->type = str_replace("Family", "Family Child Care", $data[1]);
            } else {
            	$facility->is_center = 1;
            	$facility->type = str_replace("Center", "Child Care Center", $data[1]);
            }

            $facility->state_id = $data[12];
            $facility->operation_id = $data[12];
            $facility->status = $data[13];

            if ($data[3] <> "") {
                $facility->capacity = $data[3];
            }

            if ($data[4] > 0 && preg_match("/Provider may provide care for/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo = "Provider may provide care for " . $data[4] . " children below age of 2; ";
            }

            $facility->district_office = 'Utah Child Care Licensing';
            $facility->do_phone = '(801)273-6617';

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[11] <> "") {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                $facilityDetail->initial_application_date = $data[11];
                if ($data[14] <> "") {
                    $facilityDetail->current_license_expiration_date = $data[14];
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