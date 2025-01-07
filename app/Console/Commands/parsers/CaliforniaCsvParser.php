<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;
use App\Models\Counties;

class CaliforniaCsvParser extends Command
{
    protected $signature = 'custom:parse-california-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/california/CaliforniaChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $existCount = 0;
        $newCount = 0;
        $skipCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');
     
        $lazip = [
            90001,90280,90853,91504,90002,90601,91001,91505,90003,90602,91003,91506,
            90004,90603,91006,91521,90005,90604,91007,91523,90006,90605,91009,91602,90007,
            90606,91010,91604,90010,90608,91011,91608,90011,90631,91016,91702,90012,90638,
            91017,91706,90013,90639,91020,91711,90014,90640,91021,91722,90015,90650,91024,
            91723,90017,90660,91030,91724,90020,90661,91031,91731,90021,90670,91040,91732,
            90022,90701,91041,91733,90023,90703,91042,91740,90026,90706,91043,91741,90027,
            90712,91045,91744,90028,90713,91101,91745,90029,90714,91102,91746,90030,90715,
            91103,91747,90031,90716,91104,91748,90032,90723,91105,91750,90033,90745,91106,
            91752,90037,90746,91107,91754,90038,90747,91108,91755,90039,90749,91109,91765,
            90040,90755,91125,91766,90041,90801,91126,91767,90042,90802,91201,91768,90057,
            90803,91202,91769,90058,90804,91203,91770,90059,90805,91204,91771,90061,90806,
            91205,91772,90063,90807,91206,91773,90065,90808,91207,91775,90068,90809,91208,
            91776,90071,90810,91209,91780,90079,90813,91210,91789,90201,90814,91214,91790,
            90240,90815,91226,91791,90241,90822,91236,91792,90242,90831,91352,91801,90255,
            90832,91501,91802,90262,90840,91502,91803,90270,90846,91503
        ];

        while (($data = fgetcsv($handle, 5000, ";")) !== false) {
            $row++;

            if ($row <= 1) {
                continue;
            }

            $this->info("test $row = " . $data[1]);
            if (strlen($data[9])>5) {
            	$data[9] = substr($data[9], 0,5);
            }

            $facility = Facility::where('state_id', trim($data[1]))
                                ->where('state', 'CA')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', trim($data[1]))
                                    ->where('state', 'CA')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[2]))
                                    ->where('phone', $data[5])
                                    ->first();
            }

            if (!$facility && $data[6] <> "Unavailable" && $data[6] <> "") {
                $facility = Facility::where('name', $data[2])
                                    ->where('address', $data[6])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility && $data[6] <> "Unavailable" && $data[6] <> "") {
                $facility = Facility::where('phone', $data[5])
                                    ->where('address', $data[6])
                                    ->where('zip', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[7], $data[8]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {

                if ($data[13] == 'Closed' || $data[13] == 'Unlicensed' || $data[13] == 'Inactive' ||
                    $data[13] == 'CLOSED' || $data[13] == 'UNLICENSED' || $data[13] == 'INACTIVE'
                ) {
                    $skipCount++;
                    $this->info("skip closed record $skipCount");
                    continue;
                } else {
                    $newCount++;
                    $this->info("new record $newCount");
                }

                $facility = new \stdClass();
                $facility->filename = $uniqueFilename;
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[2]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }

            $facility->name = $data[2];
            
            if ($data[6] <> "Unavailable" && $data[6] <> "") {
                $facility->address = $data[6];
            }
            
            $facility->city = $data[7];
            $facility->state = $data[8];
            $facility->zip = $data[9];
            $facility->county = $data[10];
            $facility->phone = $data[5];
            
            if ($data[15] <> "" && $facility->email == "") {
                $facility->email = $data[15];
            }
            
            $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();
            
            if ($city) {
                $facility->cityfile = $city->filename;
                $facility->county = $city->county;
            }
            
            if ($data[0] == "FAMILY DAY CARE HOME") {
                $facility->approved = 2;
            } else {
                $facility->approved = 1;
            }
            
            
            $facility->state_id = $data[1];
            $facility->operation_id = $data[1];
            if ($data[12]<> "") {
                $facility->capacity = $data[12];
            }

            $facility->status = $data[13];
            $facility->type = $data[0];

            if ($facility->status <> 'Licensed' &&
                $facility->status <> 'LICENSED') {
                if (date('Y-m-d') == date('Y-m-d', strtotime($facility->ludate))) {
                    $skipCount++;
                    $this->info("skip closed record $skipCount");
                    continue;
                }
                $facility->approved = -1;
            }

            if (strpos($data[4], ",") !== false) {
            	$contactName = explode(",", $data[4],2);
            	$facility->contact_lastname = trim($contactName[0]);
            	$facility->contact_firstname = trim($contactName[1]);
            } else {
            	$contactName = explode(" ", $data[4],2);
            	$facility->contact_lastname = $contactName[1];
            	$facility->contact_firstname = $contactName[0];
            }
            
            if (in_array($facility->zip, $lazip)) {
                $facility->district_office = "L.A. EAST REGIONAL OFFICE";
                $facility->do_phone = "(323) 981-3350";
            } else {
                if ($facility->county <> '') {
                    $county = Counties::where('state', $facility->state)
                                    ->where('county', $facility->county)
                                    ->first();

                    if ($county) {
                        $facility->district_office = $county->district_office;
                        $facility->do_phone = $county->do_phone;
                    }
                }
            }

            if ($data[0] =="FAMILY DAY CARE HOME") {
                $facility->is_center = 0;
            } else {
                $facility->is_center = 1;
                if (strpos($facility->typeofcare,$data[0]) === FALSE && strlen($facility->typeofcare)<100) {
                    $facility->typeofcare .= $data[0] . "; ";
                }
            }

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[14] <> "") {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[14] <> "") {
                    $expDates = explode("/", $data[14], 3);
                    $facilityDetail->initial_application_date = $expDates[2] . "-" . $expDates[0] . "-" . $expDates[1];
                }

                if (isset($facilityDetail->id)) {
                    $facilityDetail->save();
                } else {
                    DB::table('facilitydetail')->insert((array) $facilityDetail);
                }
            }
        }

        $this->info("$existCount exists; $newCount new; $skipCount skipped");

        fclose($this->handle);
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