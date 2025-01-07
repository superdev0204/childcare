<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class NebraskaDownloadedCsvParser extends Command
{
    protected $signature = 'custom:parse-nebraska-downloaded-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/nebraska/ChildCareRoster.csv';
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

            if (preg_match("/Family Child Care Home/",$data[6])) {
                $address = explode(" ", $data[8], 2);
                if ($address[1] != "") {
                    $data[8] = $address[1];
                }
            }

            $this->info("test $row = " . $data[2]);

            $facility = Facility::where('operation_id', $data[5])
                                ->where('state', 'NE')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('state_id', $data[5])
                                    ->where('state', 'NE')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', trim($data[2]))
                                    ->where('phone', $data[4])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[2]))
                                    ->where('address', $data[8])
                                    ->where('zip', $data[12])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[4])
                                    ->where('address', $data[8])
                                    ->where('zip', $data[12])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[10], $data[11]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
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

            $facility->name = $data[2];
            $facility->address = $data[8];
            $facility->address2 = $data[9];
            $facility->city = $data[10];
            $facility->state = $data[11];
            $facility->zip = substr($data[12],0,5);
            $facility->county = $data[1];
            $facility->phone = $data[4];

            $city = Cities::where('state', $facility->state)
                        ->where('city', $facility->city)
                        ->first();
            
            if ($city) {
                $facility->cityfile = $city->filename;
                $facility->county = $city->county;
            }
            
            if ($data[6] == "Family Child Care Home II") {
                $facility->approved = 2;
            }

            $facility->capacity = $data[13];

            if (preg_match("/Family Child Care Home/",$data[6])) {
                $facility->is_center =  0;
            } else  {
                $facility->is_center =  1;
            }

            if($facility->is_center ==  0) {
                $contactName = explode(" ", $data[3], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }

            $facility->operation_id = $data[5];
            $facility->state_id = $data[5];
            $facility->type = $data[6];

            $facility->age_range = $data[15];
            $facility->hoursopen = $data[16];
            $facility->subsidized = ($data[17] == "Y") ? 1 : 0;

            if ($data[7] <> "" && preg_match("/Initial License Date/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Initial License Date: " . $data[7] . ". ";
            }

            if ($data[18] == "Y") {
                if ( $facility->is_center ) {
                    $facility->accreditation = "National Association for the Education of Young Children";
                } else {
                    $facility->accreditation = "National Association for Family Child Care";
                }
            }

            if ( $data[14] == "MTWTF" || $data[14] == "MTWTHF") {
                $facility->daysopen = "Monday - Friday";
            } elseif ( preg_match("/MTWThFSSu/i",$data[14]) || preg_match("/MTWTFSS/i",$data[14]) ||
                preg_match("/MTWThFSS/i",$data[14]) || preg_match("/MTWTFSSu/i",$data[14])) {
                $facility->daysopen = "7 Days a Week";
            } elseif ( $data[14] == "MTWTFS" || $data[14] == "MTWTHFS") {
                $facility->daysopen = "Monday - Saturday";
            } else {
                $facility->daysopen = $data[14];
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
            
            $hoursopen = $data[16];
            
            $daysShort = preg_replace(
                ['/TW/i', '/TTH/i', '/WT(?!h)/i', '/SSu/i', '/SS/i', '/SaS(?!u)/i'],
                ['TuW', 'TuTh', 'WTh', 'SaSu', 'SaSu', 'SaSu'],
                $data[14]
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