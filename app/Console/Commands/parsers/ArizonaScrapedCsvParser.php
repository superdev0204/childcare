<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Cities;

class ArizonaScrapedCsvParser extends Command
{
    protected $signature = 'custom:parse-arizona-scraped-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/arizona/ArizonaChildCare.csv';
    protected $filename = [];

    public function handle()
    {
        $existCount = 0;
        $newCount = 0;
        $skipCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 2000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $facility = Facility::where('state_id', trim($data[0]))
                                ->where('state', 'AZ')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('operation_id', $data[10])
                                    ->where('state', 'AZ')
                                    ->first();
            }

            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[7]);

            if (!$facility) {
                $facility = Facility::whereRaw('LOWER(name) = ?', [strtolower($data[1])])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->first();
            }

            $address = str_replace(['street', 'avenue', 'drive', 'road', '.'], ['st', 'ave', 'drive', 'rd', ''], strtolower($data[6]));

            if (!$facility) {
                $facility = Facility::whereRaw('LOWER(name) = ?', [strtolower($data[1])])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", ["%$address%"])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", ["%$address%"])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[4], 'az');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = trim($data[1]);
                $facility->address = trim($data[6]);
                $facility->zip = trim($data[5]);
                $facility->city = trim($data[4]);
                $facility->county = trim($data[3]);
                $facility->phone = trim($data[7]);
                $facility->state = 'AZ';
                $facility->approved = 1;

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
                    $skipCount++;;
                    continue;
                }
            }

            $facility->type = trim($data[11]);
            $facility->is_center = strpos(strtolower($data[11]), 'home') !== false ? 0 : 1;
            $facility->operation_id = trim($data[10]);
            $facility->state_id = trim($data[0]);

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