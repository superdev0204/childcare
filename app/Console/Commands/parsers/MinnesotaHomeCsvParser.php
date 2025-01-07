<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Cities;
use App\Models\Counties;

class MinnesotaHomeCsvParser extends Command
{
    protected $signature = 'custom:parse-minnesota-home-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/minnesota/familychildcare.csv';
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

            $this->info("test $row = " . $data[2]);

            $address = explode(" ", $data[3], 2);
            
            if ($address[1] != "") {
                $data[3] = $address[1];
            }
            
            $data[8] = substr($data[8],0,5);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'MN')
                                ->first();

            $facility = $query->execute()->fetch(\PDO::FETCH_OBJ);
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[0])
                                    ->where('state', 'MN')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[2]))
                                    ->where('phone', $data[10])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[2])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[10])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[2], $data[6], $data[7]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = $data[2];
                $facility->address = $data[3];
                $facility->address2 = $data[4];
                $facility->city = $data[6];
                $facility->state = $data[7];
                $facility->zip = $data[8];
                $facility->county = $data[9];
                $facility->introduction = '';

                $facility->is_center = 0;
                $facility->operation_id = $data[0];

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }

                if ($data[11]<>'Active') {
                    $skipCount++;
                    continue;
                }

                $newCount++;

                $facility->approved = 1;
                $facility->filename = $uniqueFilename;
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[2]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            $facility->state_id = $data[0];
            $facility->phone = $data[10];

            if ($data[13]>10) {
                $facility->type = "Group family child care";
                $facility->approved = 2;
            } else {
                $facility->type = $data[1];
            }

            if ($data[11]<>'Active') {
                $facility->approved = -1;
            }

            $facility->status = $data[11];

            if ($data[12] <> "") {
                $contactName = explode(" ", $data[12], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            }

            $facility->capacity = $data[13];

            if ($facility->introduction=='') {
                $facility->introduction = $data[14];
            }

            if ($data[15] != 'None' && $facility->additionalInfo=="") {
                $facility->additionalInfo = $data[15];
            }

            $facility->typeofcare = $data[16];

            if ($facility->county <> '') {
                $county = Counties::where('state', $facility->state)
                                ->where('county', $facility->county)
                                ->first();

                if ($county) {
                    $facility->district_office = $county->district_office;
                    $facility->do_phone = $county->do_phone;
                }
            } else {
                $facility->district_office = $data[17];
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