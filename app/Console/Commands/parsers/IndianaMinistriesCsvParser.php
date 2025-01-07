<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class IndianaMinistriesCsvParser extends Command
{
    protected $signature = 'custom:parse-indiana-ministries-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/indiana/MinistriesByCounty.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($this->handle, 1000, "~")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[0]);

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
                $facility = Facility::where('phone', $data[8])
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
                $facility = Facility::where('phone', $data[8])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[8])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[8])
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
                $facility->phone = $data[8];

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

            if (!isset($facility->state_id) || $facility->state_id <> $data[0]) {
                $facility->state_id = $data[0];
            }

            if (!isset($facility->operation_id) || $facility->operation_id <> $data[0]) {
                $facility->operation_id = $data[0];
            }

            $facility->contact_lastname = $data[2];
            $facility->type = "Registered Child Care Ministry";

            $facility->district_office = "Indiana Family and Social Services Administration - Bureau of Child Care";
            $facility->do_phone = "1-877-511-1144";
            $facility->is_religious = 1;
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

            if ($data[10] <> "") {
                $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[10]));
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