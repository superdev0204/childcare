<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class IndianaCenterCsvParser extends Command
{
    protected $signature = 'custom:parse-indiana-center-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/indiana/indiana-centers.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 1000, "~")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[1]);

            $facility = Facility::where('state_id', $data[0])
                                ->where('state', 'IN')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[0])
                                    ->where('state', 'IN')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', trim($data[3]))
                                    ->where('city', trim($data[4]))
                                    ->where('state', 'IN')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[12])
                                    ->where('address', trim($data[3]))
                                    ->where('city', trim($data[4]))
                                    ->where('state', 'IN')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[12])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[1])
                                    ->where('phone', $data[12])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[12])
                                    ->where('city', trim($data[4]))
                                    ->where('state', 'IN')
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[4], 'in');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[1];
                $facility->address = $data[3];
                $facility->city = trim($data[4]);
                $facility->state = 'IN';
                $facility->zip = $data[5];
                $facility->county = $data[7];
                $facility->phone = $data[12];

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
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }

            if ($data[13]<>0 && preg_match("/Number Of Infants Licensed/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Number Of Infants Licensed: " . $data[13] . "; ";
            }

            if ($data[14]<>0 && preg_match("/Number Of Toddler Licensed/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Number Of Toddler Licensed: " . $data[14] . "; ";
            }

            if ($data[18]==1 && preg_match("/Head Start Center/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Head Start Center; ";
            }

            if ($facility->state_id <> $data[0]) {
                $facility->state_id = $data[0];
            }

            if ($facility->operation_id <> $data[0]) {
                $facility->operation_id = $data[0];
            }

            $contactName = explode(" ", $data[2], 2);
            $facility->contact_lastname = isset($contactName[1]) ? $contactName[1] : '';
            $facility->contact_firstname = $contactName[0];

            $facility->capacity = $data[15];
            $facility->age_range = $data[16] . " to " . $data[17] . " years old";
            $facility->headstart = $data[18];
            $facility->type = "Licensed Child Care Center";

            $facility->district_office = "Indiana Family and Social Services Administration - Bureau of Child Care";
            $facility->do_phone = "1-877-511-1144";
            $facility->is_center = 1;
            $facility->approved = 1;

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

            if ($data[19] <> "") {
                $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[19]));
            }

            if ($data[20] <> "") {
                $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[20]));
            }

            if ($data[8] <> "") {
                $facilityDetail->mailing_address = $data[8];
            }

            if ($data[9] <> "") {
                $facilityDetail->mailing_address2 = $data[9];
            }

            if ($data[10] <> "") {
                $facilityDetail->mailing_city = $data[10];
            }

            if ($data[11] <> "") {
                $facilityDetail->mailing_zip = $data[11];
            }

            $facilityDetail->mailing_state = 'IN';

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