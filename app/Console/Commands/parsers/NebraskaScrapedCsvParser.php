<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class NebraskaScrapedCsvParser extends Command
{
    protected $signature = 'custom:parse-nebraska-scraped-csv';
    protected $description = 'Parse data using a specified parser';  
    protected $file = '/datafiles/nebraska/NebraskaChildCare.csv';
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

            if (preg_match("/Family Child Care Home/",$data[10])) {
                $address = explode(" ", $data[7], 2);
                if ($address[1] != "") {
                    $data[7] = $address[1];
                }
            }

            $this->info("test $row = " . $data[1]);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'NE')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[11])
                                    ->where('state', 'NE')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[1]))
                                    ->where('phone', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[7])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[8])
                                    ->where('address', $data[7])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[5], $data[3]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = $data[1];
                $facility->address = $data[7];
                $facility->city = $data[5];
                $facility->state = $data[3];
                $facility->zip = $data[6];
                $facility->county = $data[4];
                $facility->phone = $data[8];

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }

                $facility->operation_id = $data[11];
                $facility->type = $data[10];
                $facility->age_range = $data[17] . " to " . $data[18];

                $facility->capacity = $data[22];
                $facility->subsidized = ($data[23] == "Yes") ? 1 : 0;

                if (preg_match("/Family Child Care Home/",$data[10])) {
                    $facility->is_center =  0;
                } else  {
                    $facility->is_center =  1;
                }

                if ($data[10] == "Family Child Care Home II") {
                    $facility->approved = 2;
                } else {
                    $facility->approved = 1;
                }

                if($facility->is_center ==  0) {
                    $contactName = explode(" ", $data[2], 2);
                    $facility->contact_lastname = $contactName[1];
                    $facility->contact_firstname = $contactName[0];
                }

                $facility->filename = $uniqueFilename;
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[2]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }

            $facility->state_id = $data[0];
            $facility->status = $data[14];

            if ($data[24] == "Yes" && preg_match("/Participates in the USDA Child Care Food Program/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= " Participates in the USDA Child Care Food Program; ";
            }

            $facility->district_office =  "Nebraska DHHS - Division of Public Health - Child Care Licensing";
            $facility->do_phone =  "1-800-600-1289";

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

            foreach (self::DAYS_OF_THE_WEEK as $day) {
                $facilityHour->{$day} = null;
            }

            $hoursopen = $data[19] . " to " . $data[20];

            $daysShort = preg_replace(
                ['/TW/i', '/TTH/i', '/WT(?!h)/i', '/SSu/i', '/SS/i', '/SaS(?!u)/i'],
                ['TuW', 'TuTh', 'WTh', 'SaSu', 'SaSu', 'SaSu'],
                'MTWTFSS'
            );

            if (preg_match('/M/i', $daysShort)) {
                $facilityHour->monday = $hoursopen;
            }

            if (preg_match('/Tu/i', $daysShort)) {
                $facilityHour->tuesday = $hoursopen;
            }

            if (preg_match('/W/i', $daysShort)) {
                $facilityHour->wednesday = $hoursopen;
            }

            if (preg_match('/Th/i', $daysShort)) {
                $facilityHour->thursday = $hoursopen;
            }

            if (preg_match('/F/i', $daysShort)) {
                $facilityHour->friday = $hoursopen;
            }

            if (preg_match('/Sa/i', $daysShort)) {
                $facilityHour->saturday = $hoursopen;
            }

            if (preg_match('/Su/i', $daysShort)) {
                $facilityHour->sunday = $hoursopen;
            }

            if (isset($facilityHour->id)) {
                $facilityHour->save();
            } else {
                DB::table('facilityhours')->insert((array) $facilityHour);
            }

            if ($data[12] <> "" || $data[13] <> "") {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[12] <> "") {
                    $facilityDetail->initial_application_date = date('Y-m-d', strtotime($data[12]));
                }

                if ($data[13] <> "") {
                    $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[13]));
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