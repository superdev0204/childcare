<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Cities;
use App\Models\Counties;

class TexasListedCsvParser extends Command
{
    protected $signature = 'custom:parse-texas-listed-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/texas/ListedChildCare.csv';
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

            if ($row == 1) {
                continue;
            }

            if ($data[9] == "Registered Child-Care Home" || $data[9] == "Listed Family Home") {
                if ($data[4] == "") {
                    $skipCount++;
                    continue;
                }
                
                $address = explode(" ", $data[3], 2);
                
                if ($address[1] != "") {
                    $data[3] = $address[1];
                }
            }

            $this->info("test $row = " . $data[0]);

            $facility = Facility::where('state_id', $data[14])
                                ->where('state', 'TX')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[0])
                                    ->where('state', 'TX')
                                    ->first();
            }

            if (!$facility && $data[8]<> "") {
                $facility = Facility::where('name', trim($data[2]))
                                    ->where('phone', $data[8])
                                    ->first();
            }

            if (!$facility && $data[3]<> "") {
                $facility = Facility::where('name', $data[2])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility && $data[8]<> "") {
                $facility = Facility::where('phone', $data[8])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[4], $data[5]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = $data[2];

                $facility->address = $data[3];
                $facility->city = $data[4];
                $facility->state = $data[5];
                $facility->zip = $data[6];
                $facility->phone = $data[8];
                $facility->county = $data[7];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[2]);
                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }

            if ($data[9] == "Licensed Child-Care Home") {
                $facility->approved = 2;
            } elseif ($facility->approved < 0) {
                $facility->approved = 1;
            }

            if (preg_match("/Licensed Center/i",$data[9])) {
                $facility->is_center = 1;
            } else {
                $facility->is_center = 0;
            }

            $facility->type = $data[9];
            $facility->status = $data[10];

            if ($data[12] <> "") {
                $facility->capacity = $data[12];
            }

            $facility->state_id = $data[14];
            $facility->operation_id = $data[0];

            if ($facility->email == "" && $data[13] <> "") {
                $facility->email = $data[13];
            }

            if ($data[11] <> "" && preg_match("/Initial License Date/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Initial License Date: " . $data[11] . ". ";
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