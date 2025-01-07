<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class MississippiCsvParser extends Command
{
    protected $signature = 'custom:parse-mississippi-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/mississippi/MsdhChildCare.csv';
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

            $facility = Facility::where('state_id', trim($data[0]))
                                ->where('state', 'MS')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('operation_id', trim($data[0]))
                                    ->where('state', 'MS')
                                    ->first();
            }

            if (!$facility) {
                $address = str_replace(['street', 'avenue', 'drive', 'road', '.'], ['st', 'ave', 'drive', 'rd', ''], strtolower($data[2]));

                $facility = Facility::where('name', $data[1])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", ["%" . str_replace(['avenue', 'street', 'drive', 'road', '.'], ['ave', 'st', 'dr', 'rd', ''], strtolower($address)) . "%"])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[6]);

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[4], 'ms');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[1];
                $facility->address = $data[2];
                $facility->city = trim($data[4]);
                $facility->state = 'MS';
                $facility->zip = $data[5];
                $facility->phone = $data[6];

                $facility->approved = 1;

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

            if ($data[8] <> '') {
                $facility->capacity = $data[8];
            }

            $facility->type = $data[7];
            if ($data[7] == "Home based Child Care Facility") {
            	$facility->is_center = 0;
            } else {
            	$facility->is_center = 1;
            }
            $facility->typeofcare = $data[10];
            $facility->age_range = $data[11];
            $facility->status = $data[9];

            if (preg_match("/PENDING|CLOSED|EXPIRED/i",$facility->status)) {
                $facility->approved = -1;
            } else {
                $facility->approved = 1;
            }
            
            $facility->state_id = $data[0];
            $facility->operation_id = $data[0];
			$facility->subsidized = ($data[14]=="Yes") ? 1 : 0;
						
            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[13] <> '') {
                $result = preg_match('/(?:((?:Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)\s*-\s*(?:Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday))
                        \s+
                        (\d{1,2}:\d{2}\s*(?:am|pm)\s*To\s*(?:\d{1,2}:\d{2}\s*(?:am|pm))))
                        ,*\s*
                        (?:(Saturday)\s*(\d{1,2}:\d{2}\s*(?:am|pm)\s*To\s*(?:\d{1,2}:\d{2}\s*(?:am|pm))))*
                        ,*\s*
                        (?:(Sunday)\s*(\d{1,2}:\d{2}\s*(?:am|pm)\s*To\s*(?:\d{1,2}:\d{2}\s*(?:am|pm))))*/ix',
                    $data[13],
                    $matches
                );

                if ($result) {
                    $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

                    if (!$facilityHour) {
                        $facilityHour = new \stdClass();
                        $facilityHour->facility_id = $facility->id;
                    }

                    if (strpos($matches[1], '-') !== false) {
                        list($dayStart, $dayEnd) = explode('-', $matches[1]);
                        $dayStart = lcfirst(trim($dayStart));
                        $dayEnd = lcfirst(trim($dayEnd));

                        $dayStartKey = array_search($dayStart, self::DAYS_OF_THE_WEEK);
                        $dayEndKey = array_search($dayEnd, self::DAYS_OF_THE_WEEK);

                        foreach (self::DAYS_OF_THE_WEEK as $day) {
                            $facilityHour->{$day} = null;
                        }

                        for ($i = $dayStartKey; $i != $dayEndKey + 1; $i++) {
                            if ($i > 6) {
                                $i = 0;
                            }

                            $day = self::DAYS_OF_THE_WEEK[$i];

                            $facilityHour->{$day} = $matches[2];
                        }
                    }

                    if (!empty($matches[3]) && !empty($matches[4])) {
                        $day = strtolower(trim($matches[3]));
                        $facilityHour->{$day} = $matches[4];
                    }

                    if (!empty($matches[5]) && !empty($matches[6])) {
                        $day = strtolower(trim($matches[5]));
                        $facilityHour->{$day} = $matches[6];
                    }

                    if (isset($facilityHour->id)) {
                        $facilityHour->save();
                    } else {
                        DB::table('facilityhours')->insert((array) $facilityHour);
                    }

                } else {
                    $this->error(sprintf('Cannot parse facility hours data. Row number = %s. Data = %s', $row, $data[13]));
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