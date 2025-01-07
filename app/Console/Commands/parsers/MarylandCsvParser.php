<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;
use App\Models\Counties;

class MarylandCsvParser extends Command
{
    protected $signature = 'custom:parse-maryland-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/maryland/MarylandChildCare.csv';
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
        
        $type = [
            'CTR' => 'Licensed Child Care Center',
            'LOC' => 'Letter of Compliance Facility',
            'FCCH' => 'Registered Family Child Care Home',
            'LFCCH' => 'Large Child Care Home'
        ];

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $data[6] = str_replace(" County","", $data[6]);

            $this->info("test $row = " . $data[0]);

            $facility = Facility::where('operation_id', $data[10])
                                ->where('state', 'MD')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('state_id', $data[10])
                                    ->where('state', 'MD')
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[1]))
                                    ->where('phone', $data[9])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[1]))
                                    ->where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[9])
                                    ->where('address', $data[2])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[3], $data[4]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = $data[1];
                $facility->address = $data[2];
                $facility->city = $data[3];
                $facility->state = $data[4];
                $facility->zip = $data[5];
                $facility->county = $data[6];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[1]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }

            if ($data[8] == "LOC") {
                $facility->is_religious = 1;
            }

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if ($data[8] == "LFCCH") {
                $facility->approved = 2;
            }

            if(preg_match( "/FCCH/i",$data[8])) {
                $contactName = explode(" ", $data[1], 2);
                $facility->contact_lastname = $contactName[1] ?? '';
                $facility->contact_firstname = $contactName[0];
                $facility->is_center = 0;
            } else {
                $facility->is_center = 1;
            }

            $facility->state_id = $data[0];
            $facility->schools_served = $data[7];
            $facility->type = $type[$data[8]];
            $facility->phone = $data[9];
            $facility->operation_id = $data[10];

            if ($data[11] <> "") {
                $facility->email = $data[11];
            }

            $facility->hoursopen = $data[12];
            $facility->status = $data[13];

            if ($data[14] <> "") {
                $facility->capacity = $data[14];
            }

            if ($data[15] <> "") {
                $facility->age_range = $data[15];
            }

            if ($data[16] <> "NA") {
                $facility->accreditation = $data[16];
            }

            if ($data[17] == "Yes" && preg_match("/Approved Education Program/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "MSDE-Approved Education Program. ";
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

            if ($data[12]) {
                $schedule = explode('   ', $data[12], 3);

                if (count($schedule) == 3) {
                    $days = $schedule[0];
                    $time = $schedule[1];
                    $months = $schedule[2];
                } elseif (count($schedule) == 2) {
                    if (preg_match('/(M|Tu|W|Th|F|Sa|Su)\s*-\s*(M|Tu|W|Th|F|Sa|Su)/i', $schedule[0])) {
                        $days = $schedule[0];
                    }

                    if (preg_match('/(M|Tu|W|Th|F|Sa|Su)\s*-\s*(M|Tu|W|Th|F|Sa|Su)/i', $schedule[1])) {
                        $days = $schedule[1];
                    }

                    if (preg_match('/mon|tue|wed|thu|fri|sat|sun/i', $schedule[0])) {
                        $days = $schedule[0];
                    }

                    if (preg_match('/mon|tue|wed|thu|fri|sat|sun/i', $schedule[1])) {
                        $days = $schedule[1];
                    }

                    if (preg_match('/\d{1,2}:\d{2}\s*am\s*-\s*(?:\d{1,2}:\d{2}\s*(?:pm|midnight)|midnight)/i', $schedule[0])) {
                        $time = $schedule[0];
                    }

                    if (preg_match('/\d{1,2}:\d{2}\s*am\s*-\s*(?:\d{1,2}:\d{2}\s*(?:pm|midnight)|midnight)/i', $schedule[1])) {
                        $time = $schedule[1];
                    }

                    if (preg_match('/jan|feb|march|apr|may|jun|jul|aug|sep|oct|nov|dec/i', $schedule[0])) {
                        $months = $schedule[0];
                    }

                    if (preg_match('/jan|feb|march|apr|may|jun|jul|aug|sep|oct|nov|dec/i', $schedule[1])) {
                        $months = $schedule[1];
                    }

                    if (!isset($days)) {
                        $days = 'Monday - Friday';
                    }

                    if (!isset($time)) {
                        $time = '7:00AM-6:00PM*';
                    }
                }

                if (isset($days) && isset($time)) {
                    if (strpos($days, 'to')) {
                        list($dayStart, $dayEnd) = explode(' to ', $days);
                        $dayStart = lcfirst(trim($dayStart, ' s.'));
                        $dayEnd = lcfirst(trim($dayEnd, ' s.'));
                    } elseif (strpos($days, '-')) {
                        list($dayStart, $dayEnd) = explode('-', $days);
                        $dayStart = lcfirst(trim($dayStart, ' s.'));
                        $dayEnd = lcfirst(trim($dayEnd, ' s.'));
                    } elseif (strpos($days, 'through')) {
                        list($dayStart, $dayEnd) = explode(' through ', $days);
                        $dayStart = lcfirst(trim($dayStart, ' s.'));
                        $dayEnd = lcfirst(trim($dayEnd, ' s.'));
                    }

                    if (isset($dayStart) && isset($dayEnd)) {
                        $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                        if (!$facilityHour) {
                            $facilityHour = new \stdClass();
                            $facilityHour->facility_id = $facility->id;
                        }

                        $shortWeekDays = array_map(function($day) {
                            return strtolower($day);
                        }, self::DAYS_OF_THE_WEEK_SHORT);

                        $abbrWeekDays = array_map(function($day) {
                            return strtolower($day);
                        }, self::DAYS_OF_THE_WEEK_ABBR);

                        if (strlen($dayStart) < 3) {
                            $dayStartKey = array_search($dayStart, $shortWeekDays);
                        } elseif (strlen($dayStart) == 3) {
                            $dayStartKey = array_search($dayStart, $abbrWeekDays);
                        } else {
                            $dayStartKey = array_search($dayStart, self::DAYS_OF_THE_WEEK);
                        }

                        if (strlen($dayEnd) < 3) {
                            $dayEndKey = array_search($dayEnd, $shortWeekDays);
                        } elseif (strlen($dayEnd) == 3) {
                            $dayEndKey = array_search($dayEnd, $abbrWeekDays);
                        } else {
                            $dayEndKey = array_search($dayEnd, self::DAYS_OF_THE_WEEK);
                        }

                        if ($dayEndKey !== false && $dayStartKey !== false) {
                            foreach (self::DAYS_OF_THE_WEEK as $day) {
                                $facilityHour->{$day} = null;
                            }

                            for ($i = $dayStartKey; $i != $dayEndKey + 1; $i++) {
                                if ($i > 6) {
                                    $i = 0;
                                }

                                $day = self::DAYS_OF_THE_WEEK[$i];

                                $facilityHour->{$day} = $time . (isset($months) && !preg_match('/jan.*\s*.*\s*dec/i', $months) ? $months : '');
                            }

                            if (isset($facilityHour->id)) {
                                $facilityHour->save();
                            } else {
                                DB::table('facilityhours')->insert((array) $facilityHour);
                            }
                        } else {
                            $this->error(sprintf('Cannot parse facility hours data. Row number = %s. Data = %s', $row, $data[12]));
                        }
                    }
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