<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Cities;

class WisconsinDownloadedCsvParser extends Command
{
    protected $signature = 'custom:parse-wisconsin-downloaded-csv';
    protected $description = 'Parse data using a specified parser';        
    protected $file = '/datafiles/wisconsin/childcare-download.csv';
    protected $filename = [];
    
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

            if (preg_match("/CERTIFIED FAMILY|LICENSED FAMILY/",$data[0])) {
                $address = explode(" ", $data[4], 2);
                if ($address[1] != "") {
                    $data[4] = $address[1];
                }
            }
            
            $county = explode(". ", $data[1], 2);
            $data[1] = $county[1];
            $data[3] = trim($data[3]);
            $data[6] = substr($data[6],0,5);
            $data[7] = trim($data[7]);
            $data[8] = preg_replace("/[^0-9]/", "", $data[8]);

            $this->info("test $row = " . $data[2]);
            
            $facility = null;
            if ($data[3] <> "") {
                $facility = Facility::where('state_id', $data[3])
                                    ->where('state', 'WI')
                                    ->first();
                
                if (!$facility) {
                    $facility = Facility::where('operation_id', $data[3])
                                        ->where('state', 'WI')
                                        ->first();
                }
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[2])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('address', $data[4])
                                    ->where('city', $data[5])
                                    ->where('phone', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[2])
                                    ->where('phone', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[5], 'wi');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $facility = new \stdClass();
                $facility->name = $data[2];
                $facility->address = $data[4];
                $facility->city = $data[5];
                $facility->state = "WI";
                $facility->zip = $data[6];
                $facility->phone = $data[8];
                $facility->county = $data[1];

                $newCount++;
                $this->info("new record $newCount");

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }

                $facility->filename = $uniqueFilename;
            }
            else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[2]);
                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if (preg_match("/CERTIFIED FAMILY|LICENSED FAMILY/",$data[0])) {
                $facility->is_center =  0;
                if (preg_match("/LICENSED FAMILY/",$data[0])) {
                    $facility->approved = 2;
                }
            } else  {
                $facility->is_center =  1;
            }

            if ($data[3] <> "") {
                $facility->state_id = $data[3];
                $facility->operation_id = $data[3];
            }

            $facility->type = ucwords(strtolower($data[0])) . " Child Care";

            $facility->capacity = $data[10];
            $facility->age_range =  $data[11] . " - " . $data[12];
            $facility->hoursopen = $data[13];

            if ($data[15] <> "Y" && $facility->daysopen == "" && $facility->user_id == 0) {
                $facility->daysopen = "Monday-Friday";
            }

            if ($data[7] <> "") {
                $contactName = explode(", ", $data[7], 2);
                $facility->contact_firstname = $contactName[1];
                $facility->contact_lastname = $contactName[0];
            }

            if ($data[9] <> "" && preg_match("/Initial License Date:/",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Initial License Date: " . $data[9] . ". ";
            }

            if ($data[14] <> "" && $data[14] <> "Jan-Dec" && preg_match("/Open /",$rowData->additionalInfo) == false) {
                $rowData->additionalInfo .= "Open " . $data[14] . ". ";
            }

            $facility->district_office = 'Wisconsin Dept of Children and Families (DCF)-  Child Care Regulation and Licensing';
            $facility->do_phone = '608-266-9314';

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