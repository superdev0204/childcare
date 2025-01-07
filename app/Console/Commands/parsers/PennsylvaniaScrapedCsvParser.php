<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;
use App\Models\Counties;

class PennsylvaniaScrapedCsvParser extends Command
{
    protected $signature = 'custom:parse-pennsylvania-scraped-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/pennsylvania/PennsylvaniaChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $existCount = 0;
        $newCount = 0;
        $skipCount = 0;

        $type = [
            'HDS' => 'Head Start Center',
            'NFP' => 'Home Visiting Program',
            'DCH' => 'Day Care Home'
        ];
        
        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');
        
        while (($data = fgetcsv($handle, 3000, ";")) !== false) {
            $row++;
            
            if ($row == 1) {
                continue;
            }

            if ($data[2] == "FMY") {
                $address = explode(" ", $data[4], 2);
                if ($address[1]) {
                    $data[4] = $address[1];
                }
            }

            $this->info("test {$row} = {$data[43]}");

            $facility = Facility::where('state_id', $data[43])
                                ->where('state', 'PA')
                                ->first();
            
            if (!$facility && $data[42]) {
                $facility = Facility::where('operation_id', $data[42])
                                    ->where('state', 'PA')
                                    ->first();
            }

            if (!$facility && strlen($data[10]) >= 10 ) {
                $facility = Facility::where('name', $data[0])
                                    ->where('phone', $data[10])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility && strlen($data[10]) >= 10) {
                $facility = Facility::where('phone', $data[10])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('address', $data[4])
                                    ->where('zip', $data[7])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[5], $data[6]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info('new record ' . $newCount);

                $facility = new \stdClass();
                
                $facility->city = $data[5];
                $facility->state = $data[6];
                $facility->filename = $uniqueFilename;

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[0]);
                
                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }
            $facility->name = $data[0];
            $facility->address = $data[4];
            $facility->zip = $data[7];
            if (strlen($data[10])>=10) {
                $facility->phone = $data[10];
            }
            
            if ($facility->approved <= 0) {
                $facility->approved = 1;
            }

            if ($data[2] == "GRP") {
                $facility->approved = 2;
            }

            if ($data[2] == "FMY" || $data[2] == "GRP") {
                $facility->is_center = 0;
                $facility->type = 'Family Child Care';
            } else {
                $facility->is_center = 1;
                if ($data[14] == "HDS") {
                    $facility->type = 'Head Start Center';
                    $facility->headstart = 1;
                } elseif ($data[14] =='PKC') {
                    $facility->type = 'PA Pre-K Counts';
                } else {
                    $facility->type = 'Child Care Center';
                }
            }

            $facility->state_id = $data[43];
            if ($data[42] <> "") {
                $facility->operation_id = $data[42];
            }
            if ($data[17]<> '') {
                $facility->capacity = $data[17];
            }
            if ($data[3] <> "") {
                $facility->state_rating = $data[3];
            }
            if ($data[8] <> '0' && $facility->lat == null) {
                $facility->lat = $data[8];
            }
            if ($data[9] <> '0' && $facility->lng == null) {
                $facility->lng = $data[9];
            }
            if ($data[12] <> '') {
                $facility->website = $data[12];
            }
            if ($data[21 <> '']){
                $facility->schools_served = $data[21];
            }
            if ($data[33] <> '') {
                $facility->language = $data[33];
            }
            if ($data[37]=='Eligible Subsidy Provider') {
                $facility->subsidized = 1;
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

            $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();
            
            if (!$facilityHour) {
                $facilityHour = new \stdClass();
                $facilityHour->facility_id = $facility->id;
            }
            if ($data[25]) {
                $facilityHour->monday = $data[25];
            }
            if ($data[26]) {
                $facilityHour->tuesday = $data[26];
            }
            if ($data[27]) {
                $facilityHour->wednesday = $data[27];
            }
            if ($data[28]) {
                $facilityHour->thursday = $data[28];
            }
            if ($data[29]) {
                $facilityHour->friday = $data[29];
            }
            if ($data[30]) {
                $facilityHour->saturday = $data[30];
            }
            if ($data[24]) {
                $facilityHour->sunday = $data[24];
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