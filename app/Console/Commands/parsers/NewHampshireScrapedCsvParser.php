<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Cities;

class NewHampshireScrapedCsvParser extends Command
{
    protected $signature = 'custom:parse-new-hampshire-scraped-csv';
    protected $description = 'Parse data using a specified parser';        
    protected $file = '/datafiles/new-hampshire/NHampshireChildCare.csv';
    protected $filename = [];
    
    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 2000, ";")) !== false) {
            $row++;

            if ($row <= 1) {
                $this->info("skip " . ++$skipCount);
                continue;
            }

            $this->info("test $row = " . $data[1]);
            
            if ($data[2] == "Family Based Program License" && $data[7] <> "") {
                $address = explode(" ", $data[7], 2);
                if ($address[1] != "") {
                    $data[7] = $address[1];
                }
            }
            
            if (strlen($data[1])> 75) {
                $data[1] = substr($data[1], 0,75);
            }
            
            if (strlen($data[6])==6) {
                $data[6] = substr($data[6], 1);
            }
            
            $data[6] = str_pad($data[6],5,"0",STR_PAD_LEFT);

            $formattedPhone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $data[4]);

            $facility = Facility::where('operation_id', $data[0])
                                ->where('state', 'NH')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('state_id', $data[0])
                                    ->where('state', 'NH')
                                    ->first();
            }

            if (!$facility && $data[7] <> "") {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[7])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility && $data[4] <> "") {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[4])
                                    ->first();

                if (!$facility ) {
                    $facility = Facility::where('name', $data[1])
                                        ->where('phone', $formattedPhone)
                                        ->first();
                }
            }
            if (!$facility && $data[7] <> "" && $data[4] <>"") {
                $facility = Facility::where('address', $data[7])
                                    ->where('phone', $data[4])
                                    ->first();

                if (!$facility ) {
                    $facility = Facility::where('address', $data[7])
                                        ->where('phone', $formattedPhone)
                                        ->first();
                }
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', 'LIKE', '%' . substr($formattedPhone, -4))
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[3], 'nh');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->state = "NH";
                $facility->phone = $formattedPhone;
                $facility->filename = $uniqueFilename;
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[1]);
                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->name = $data[1];
            $facility->address = $data[7];
            $facility->address2 = $data[8];
            $facility->city = $data[3];
            $facility->zip = $data[6];
            $facility->county = $data[5];
            if ($data[4] <> "") {
                $facility->phone = $formattedPhone;
            }
            
            $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();
            
            if ($city) {
                $facility->cityfile = $city->filename;
                $facility->county = $city->county;
            }
            
            $facility->state_id = $data[0];
            $facility->operation_id = $data[0];

            $facility->type = $data[2];
            $facility->status = $data[10];

            if ($facility->approved <> 2) {
                $facility->approved = 1;
            }

            if ($data[2] == "Center Based Program License") {
                $facility->is_center =  1;
            } else  {
                $facility->is_center =  0;
            }

            if ($data[9] <> "" && $facility->email =="") {
                $facility->email = $data[9];
            }

            if ($facility->user_id == 0 && $facility->daysopen=="" && $facility->hoursopen == "") {
                $facility->daysopen = "Monday - Friday";
            }

            $facility->district_office = 'New Hampshire Dept of Health and Human Services - Child Care Licensing Unit';
            $facility->do_phone = '(603) 271-4624';

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