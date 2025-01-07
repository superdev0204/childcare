<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Inspections;
use App\Models\Cities;
use App\Models\Counties;

class TexasLicensedCsvParser extends Command
{
    protected $signature = 'custom:parse-texas-licensed-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/texas/TexasChildCare.csv';
    protected $filename = [];

    protected const DAYS_OF_THE_WEEK = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    protected const DAYS_OF_THE_WEEK_SHORT = ['M', 'Tu', 'W', 'Th', 'F', 'Sa', 'Su'];
    protected const DAYS_OF_THE_WEEK_ABBR = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

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
                continue;
            }

            if ($data[1] == "Registered Child-Care Home" || $data[1] == "Listed Family Home") {
                if ($data[5] == "") {
                    $skipCount++;
                    continue;
                }
                $address = explode(" ", $data[4], 2);
                if ($address[1] != "") {
                    $data[4] = $address[1];
                }
            }

            $this->info("test $row = " . $data[0]);

            $facility = Facility::where('state_id', $data[29])
                                ->where('state', 'TX')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[0])
                                    ->where('state', 'TX')
                                    ->first();
            }

            if (!$facility && $data[12] <> "") {
                $facility = Facility::where('name', trim($data[3]))
                                    ->where('phone', $data[12])
                                    ->first();
            }

            if (!$facility && $data[3] <> "") {
                $facility = Facility::where('name', $data[3])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility && $data[12]<> "") {
                $facility = Facility::where('phone', $data[12])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[3], $data[5], $data[6]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->city = $data[5];
                $facility->state = $data[6];
                $facility->county = $data[13];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[3]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }
            
            $facility->name = $data[3];
            
            $facility->address = $data[4];
            $facility->address2 = $data[41];
            $facility->zip = $data[7];
            $facility->phone = $data[12];
            
            $facility->state_id = $data[29];
            $facility->operation_id = $data[0];

            if ($data[1] == "Licensed Child-Care Home") {
                $facility->approved = 2;
            } elseif ($facility->approved < 0) {
                $facility->approved = 1;
            }

            if (preg_match("/Licensed Center/i",$data[1])) {
                $facility->is_center = 1;
            } else {
                $facility->is_center = 0;
            }

            $facility->type = $data[1];

            if ($data[2] <> "") {
                $facility->type .= " - " . $data[2];
            }

            if ($data[14] <> "") {
                $facility->website = $data[14];
            }

            if ($facility->email == "" && $data[15] <> "") {
                $facility->email = $data[15];
            }

            if($data[16] != "") {
                $contactName = explode(" ", $data[16], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }

            $facility->status = $data[17];

            if ($data[20] == "Yes") {
                $facility->subsidized = 1;
            }

            if ($data[23] <> "") {
                $facility->capacity = $data[23];
            }

            if ($data[24] <> "") {
                $facility->age_range = $data[24];
            }

            if (preg_match("/Infant/i",$data[24])) {
                $facility->is_infant = 1;
            }

            if (preg_match("/Toddler/i",$data[24])) {
                $facility->is_toddler = 1;
            }

            if (preg_match("/Pre-Kindergarten/i",$data[24])) {
                $facility->is_preschool = 1;
            }

            if (preg_match("/School/i",$data[24])) {
                $facility->is_afterschool = 1;
            }

            if ($data[18] <> "" && preg_match("/Initial License Date/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Initial License Date: " . $data[18] . ". ";
            }

            if ($facility->county <> '') {
                $county = Counties::where('state', $facility->state)
                                ->where('county', $facility->county)
                                ->first();

                if ($county) {
                    $facility->district_office = $county->district_office;
                    $facility->do_phone = $county->do_phone;
                }
            }

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

            if (!$facilityDetail) {
                $facilityDetail = new \stdClass();
                $facilityDetail->facility_id = $facility->id;
            }

            if ($data[8] <> "") {
                $facilityDetail->mailing_address = $data[8];
            }

            if ($data[9] <> "") {
                $facilityDetail->mailing_city = $data[9];
            }

            if ($data[10] <> "") {
                $facilityDetail->mailing_state = $data[10];
            }

            if ($data[11] <> "") {
                $facilityDetail->mailing_zip = $data[11];
            }

            if ($data[18] <> "" ) {
                $facilityDetail->initial_application_date = date('Y-m-d', strtotime($data[18]));
            }

            if (isset($facilityDetail->id)) {
                $facilityDetail->save();
            } else {
                DB::table('facilitydetail')->insert((array) $facilityDetail);
            }

            if ($data[21] <> "" && $data[22] <> "") {
                $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }

                if (strpos($data[22], '-')) {
                    list($dayStart, $dayEnd) = explode('-', $data[22]);
                    $dayStart = lcfirst(trim($dayStart));
                    $dayEnd = lcfirst(trim($dayEnd));

                    $dayStartKey = array_search($dayStart, self::DAYS_OF_THE_WEEK);
                    $dayEndKey = array_search($dayEnd, self::DAYS_OF_THE_WEEK);

                    if ($dayEndKey !== false && $dayStartKey !== false) {
                        foreach (self::DAYS_OF_THE_WEEK as $day) {
                            $facilityHour->{$day} = null;
                        }

                        for ($i = $dayStartKey; $i != $dayEndKey + 1; $i++) {
                            if ($i > 6) {
                                $i = 0;
                            }

                            $day = self::DAYS_OF_THE_WEEK[$i];

                            $facilityHour->{$day} = $data[21];
                        }

                        if (isset($facilityHour->id)) {
                            $facilityHour->save();
                        } else {
                            DB::table('facilityhours')->insert((array) $facilityHour);
                        }
                    } else {
                        $this->error(sprintf('Cannot parse facility days of operation data. Row number = %s. Data = %s', $row, $data[22]));
                    }

                } elseif (strpos($data[22], ',')) {
                    $days = array_map(function($day) {
                        return strtolower(substr(trim($day), 0, 3));
                    }, explode(',', $data[22]));

                    foreach ($days as $day) {
                        $key = array_search(ucfirst($day), self::DAYS_OF_THE_WEEK_ABBR);

                        if ($key === false) {
                            continue;
                        }

                        $day = self::DAYS_OF_THE_WEEK[$key];
                        $facilityHour->{$day} = $data[21];
                    }

                    if (isset($facilityHour->id)) {
                        $facilityHour->save();
                    } else {
                        DB::table('facilityhours')->insert((array) $facilityHour);
                    }
                } elseif ($data[22] == 'Saturday & Sunday') {
                    $facilityHour->saturday = $data[21];
                    $facilityHour->sunday = $data[21];
                } elseif ($data[22] == 'Sat') {
                    $facilityHour->saturday = $data[21];
                } else {
                    $this->error(sprintf('Cannot parse facility days of operation data. Row number = %s. Data = %s', $row, $data[22]));
                }
            }

            $inspection = Inspections::where('facility_id', $facility->id)->first();

            if (!$inspection) {
                $inspection = new \stdClass();
                $inspection->facility_id = $facility->id;
                $inspection->inserted = date('Y-m-d H:i:s');
            }
            $inspection->updated = date('Y-m-d H:i:s');
            $inspection->state = 'TX';
            $inspection->report_type = $data[30];
            $inspection->pages = $data[33];
            $inspection->rule_description = $data[31];
            $inspection->current_status = $data[32];
            $inspection->report_url = "http://www.dfps.state.tx.us/Child_Care/Search_Texas_Child_Care/CCLNET/Source/Provider/ppComplianceHistory.aspx?fid=" . $data[29] . "&type=RPT";
            
            if (isset($inspection->id)) {
                $inspection->save();
            } else {
                DB::table('inspections')->insert((array) $inspection);
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