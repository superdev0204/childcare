<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\Cities;

class ConnecticutCsvParser extends Command
{
    protected $signature = 'custom:parse-connecticut-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/connecticut/ConnecticutChildCare.csv';
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

            $this->info("test $row = " . $data[0]);

            if ($data[9] == "Family Day Care Home") {
                $address = explode(" ", $data[6], 2);
                if ($address[1] != "") {
                    $data[6] = $address[1];
                }
            }

            $data[5] = str_pad($data[5], 5, "0", STR_PAD_LEFT);

            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[8]);

            if(strlen($strippedPhoneNumber) == 10) {
                $data[8] = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $strippedPhoneNumber);
            }

            $facility = Facility::where('state_id', trim($data[0]))
                                ->where('state', 'CT')
                                ->first();

            if (!$facility && strlen($data[10])> 5) {
                $facility = Facility::where('operation_id', trim($data[10]))
                                    ->where('state', 'CT')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[1]))
                                    ->where('phone', $data[8])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', trim($data[1]))
                                    ->where('address', $data[6])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', trim($data[8]))
                                    ->where('address', $data[6])
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[8])
                                    ->where('address', 'LIKE', substr($data[6], 0, 10) . '%')
                                    ->where('zip', $data[5])
                                    ->first();
            }

            if (!$facility) {
                $query = $this->getQueryBuilder()
                    ->select('*')
                    ->from('facility')
                    ->where('name = ?')
                    ->andWhere($this->getQueryBuilder()->expr()->like('address', $this->getQueryBuilder()->expr()->literal(substr($data[6],0,10) . '%')))
                    ->andWhere('zip = ?')
                    ->setParameter(0, trim($data[1]))
                    ->setParameter(1, $data[5]);

                $facility = $query->execute()->fetch(\PDO::FETCH_OBJ);
            }

            if (!$facility) {
                $facility = Facility::where('name', 'LIKE', substr($data[1], 0, 15) . '%')
                                    ->where('address', 'LIKE', substr($data[6], 0, 10) . '%')
                                    ->where('zip', $data[5])
                                    ->where('ludate', '<', '2014-09-30')
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[4], 'ct');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->county = $data[3];
                $facility->city = $data[4];
                $facility->zip = $data[5];
                $facility->address = $data[6];
                $facility->address2 = $data[7];
                $facility->phone = $data[8];

                $city = Cities::where('state', $facility->state)
                            ->where('city', $facility->city)
                            ->first();

                if ($city) {
                    $facility->cityfile = $city->filename;
                    $facility->county = $city->county;
                }

                $facility->approved = 1;
                $facility->filename = $uniqueFilename;
            } else {
                $existCount++;
                $this->info("existing $existCount " . $facility->name . " | " . $data[1]);

                $facility->city = $data[4];
                $facility->zip = $data[5];
                $facility->address = $data[6];
                $facility->address2 = $data[7];

                if ($facility->cityfile=="") {
                    $city = Cities::where('state', $facility->state)
                                ->where('city', $facility->city)
                                ->first();

                    if ($city) {
                        $facility->cityfile = $city->filename;
                        $facility->county = $city->county;
                    }
                }

                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }
            $facility->state = $data[2];
            $facility->name = $data[1];
            $facility->state_id = $data[0];
            $facility->operation_id = $data[10];
            $facility->type = $data[9];
            $facility->status = $data[11];

            if ($data[11] == "PENDING") {
                $facility->approved = -1;
            } elseif ($facility->approved <= 0) {
                $facility->approved = 1;
            }

            if ($data[9] == "Child Day Care Center" || $data[9] == "Child Care Center") {
                $facility->is_center =  1;
            } else  {
                $facility->is_center =  0;
                if ($data[9] == "Group Day Care Home") {
                    $facility->approved = 2;
                }
            }

            $facility->age_range = $data[18];

            if ($data[16] <> "") {
                $facility->capacity = $data[16];
            }

            if ($data[16] == "" && $data[19] <> "") {
                $facility->capacity = $data[19];
            }

            $facility->typeofcare = $data[15];

            if ($facility->daysopen =="") {
                $facility->daysopen = "Monday-Friday";
            }

            $facility->district_office =  "Connecticut Dept. of Public Health - Child Day Care Licensing Program";
            $facility->do_phone =  "1-860-509-7540";

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[13] <> '' || $data[14] <> '') {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[13] <> "") {
                    $facilityDetail->initial_application_date = date('Y-m-d', strtotime($data[13]));
                }

                if ($data[14] <> "") {
                    $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[14]));
                }

                if (isset($facilityDetail->id)) {
                    $facilityDetail->save();
                } else {
                    DB::table('facilitydetail')->insert((array) $facilityDetail);
                }
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