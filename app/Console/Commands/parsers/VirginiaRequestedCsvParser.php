<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Cities;

class VirginiaRequestedCsvParser extends Command
{
    protected $signature = 'custom:parse-virginia-requested-csv';
    protected $description = 'Parse data using a specified parser';    
    protected $file = '/datafiles/virginia/childcareproviders.csv';
    protected $filename = [];
    
    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $type = [
            'CDC' => 'Licensed Child Day Center',
            'CCS' => 'Licensed Summer Day Camp',
            'FDH' => 'Licensed Family Day Home',
            'CNS' => 'Certified Pre-School',
            'CCE' => 'Religious Exempt Child Day Center',
            'FDS' => 'Licensed Family Day System',
            'VR'  => 'Voluntarily Registered Family Day Home',
            'CCI' => 'Licensed Child Caring Institution',
            'CRF' => 'Licensed Children\'s Residential Facility',
            'CPA' => 'Licensed Child Placing Agency',
            'IFH' => 'Licensed Independent Foster Home'
        ];

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                $this->info("skip " . ++$skipCount);
                continue;
            }

            $this->info("test $row = " . $data[1]);
            
            if ($data[2] == "FDH" || $data[2] == "FDS" || $data[2] == "VR" || $data[2] == "IFH") {
                $address = explode(" ", $data[11], 2);
                if ($address[1] != "") {
                    $data[11] = $address[1];
                }
            }
            
            $data[11] = trim($data[11],",");
            
            if (strlen($data[13]) > 5) {
                $data[13] = substr($data[13],0,5);
            }

            $facility = Facility::where('state_id', trim($data[1]))
                                ->where('state', 'VA')
                                ->where('type', $type[$data[2]])
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', trim($data[1]))
                                    ->where('state', 'VA')
                                    ->where('type', $type[$data[2]])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[3])
                                    ->where('address', $data[11])
                                    ->where('zip', $data[13])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[6])
                                    ->where('address', $data[11])
                                    ->where('zip', $data[13])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[3])
                                    ->where('phone', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[3], $data[12], 'va');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[3];
                $facility->address = $data[11];
                $facility->address2 = $data[15];
                $facility->city = $data[12];
                $facility->state = "VA";
                $facility->zip = $data[13];
                $facility->phone = $data[6];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[3]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->state_id = $data[1];
            $facility->operation_id = $data[1];
            $facility->type = $type[$data[2]];

            if ($data[5] <> "") {
                $facility->capacity = $data[5];
            }

            if ($data[4] <> "") {
                $contactName = explode(" ", $data[4], 2);
                $facility->contact_firstname = $contactName[0];
                $facility->contact_lastname = $contactName[1];
            }

            if ($data[7] <>"") {
                $facility->age_range = $data[7] . " years";
                if ($data[8] > 0) {
                    $facility->age_range .= " " . $data[8] . " months";
                }
            }
            if ($data[9] <>"") {
                if ($facility->age_range == "") {
                    $facility->age_range = "Infants";
                }
                $facility->age_range .= " to " . $data[9] . " years ";
                if ($data[10] > 0) {
                    $facility->age_range .= " " . $data[10] . " months";
                }
            }

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if ($data[2] == "FDH" || $data[2] == "FDS" || $data[2] == "VR" || $data[2] == "IFH") {
                $facility->is_center =  0;
                if ($data[2] == "FDH" || $data[2] == "FDS") {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }

            $facility->district_office = 'Virginia Dept of Social Services - Division of Licensing Programs';
            $facility->do_phone = '(800) 543-7545';
            $facility->ludate = date('Y-m-d H:i:s');

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
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