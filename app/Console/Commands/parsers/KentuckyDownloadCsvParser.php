<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class KentuckyDownloadCsvParser extends Command
{
    protected $signature = 'custom:parse-kentucky-download-csv';
    protected $description = 'Parse data using a specified parser';        
    protected $file = '/datafiles/kentucky/ProviderResults.csv';
    protected $filename = [];
    
    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 3000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[1]);

            $facility = Facility::where('state_id', $data[1])
                                ->where('state', $data[5])
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', substr($data[1],1))
                                    ->where('state', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('operation_id', $data[1])
                                    ->where('state', $data[5])
                                    ->first();
            }
            
            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[7]);

            if (!$facility) {
                $facility = Facility::where('name', $data[2])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->first();
            }

            $address = str_replace(['street', 'avenue', 'drive', 'road', '.'], ['st', 'ave', 'dr', 'rd', ''], strtolower($data[3]));

            if (!$facility) {
                $facility = Facility::where('name', $data[2])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", ["%" . str_replace(['avenue', 'street', 'drive', 'road', '.'], ['ave', 'st', 'dr', 'rd', ''], strtolower($address)) . "%"])
                                    ->where('city', $data[4])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", ["%" . str_replace(['avenue', 'street', 'drive', 'road', '.'], ['ave', 'st', 'dr', 'rd', ''], strtolower($address)) . "%"])
                                    ->where('city', $data[4])
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

                $facility->city = trim($data[4]);
                $facility->state = trim($data[5]);
                $facility->county = $data[0];
                

                $facility->zip = substr($data[6],0,5);

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[2]);
                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }
            $facility->name = $data[2];
            $facility->address = $data[3];
            $facility->address2 = trim($data[19]);
            $facility->phone = $data[7];
            if ($data[12] <> "") {
                $facility->capacity = $data[12];
            }

            if ($data[13] <> "") {
                $facility->age_range = str_replace("_"," ",$data[13]);
            }

            if ($data[14] == "Y") {
                $facility->transportation = "Yes";
            }

            if ($data[15] == "Y") {
                $facility->hoursopen = "Night and weekend hours may be available.";
            } else {
                $facility->daysopen = "Monday - Friday";
                $facility->hoursopen = "6AM - 6PM";
            }
            if ($facility->state_id == "") {
                $facility->state_id = $data[1];
            }

            if ($facility->operation_id == "") {
                $facility->operation_id = $data[1];
            }

            if ($data[16] <> "") {
                $facility->state_rating = $data[16];
                if (preg_match("/STARS/i",$facility->additionalInfo) == false) {
                    $facility->additionalInfo .= "STARS Rating: " . $data[16] . "; ";
                }
            }
            if ($data[20] == "Y") {
                if (preg_match("/Serves Children with Special Needs/i",$facility->additionalInfo) == false) {
                    $facility->additionalInfo .= "Serves Children with Special Needs; ";
                }
            }
            
            $this->info($data[17]);

            if ($data[17] == "Licensed" && $data[12] > 12) {
                $facility->type = "Child Care Center - " . $data[17];
                $facility->approved = 1;
                $facility->is_center = 1;
            } else {
                $facility->type = "Family Child Care - " . $data[17];
                $facility->approved = 2;
                $facility->is_center = 0;
            }

            if ($data[18] == "Y") {
                $facility->subsidized = 1;
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
                $facilityDetail->mailing_address = $data[8] . ", " . trim($data[21]);
            }

            if ($data[9] <> "") {
                $facilityDetail->mailing_city = trim($data[9]);
            }

            $facilityDetail->mailing_state = trim($data[22]);

            if ($data[10] <> "") {
                $facilityDetail->mailing_zip = substr($data[10],0,5);
            }

            if ($data[11] <> "") {
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[11]));
            }

            if (isset($facilityDetail->id)) {
                $facilityDetail->save();
            } else {
                DB::table('facilitydetail')->insert((array) $facilityDetail);
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