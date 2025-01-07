<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class IllinoisCsvParser extends Command
{
    protected $signature = 'custom:parse-illinois-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/illinois/LicensedDaycares.csv';
    protected $filename = [];

    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $type = [
            'DCC' => 'Day Care Center',
            'GDC' => 'Group Day Care Home',
            'DCH' => 'Day Care Home'
        ];

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            if ($data[9] == "Day Care Home") {
                $address = explode(" ", $data[3], 2);
                if ($address[1] != "") {
                    $data[3] = $address[1];
                }
            }
            
            $data[6] = substr($data[6], 0,5);

            $this->info("test $row = " . $data[1]);

            $facility = Facility::where('state_id', trim($data[0]))
                                ->where('state', 'IL')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', trim($data[0]))
                                    ->where('state', 'IL')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[2])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', 'LIKE', $data[1] . '%')
                                    ->where('phone', $data[2])
                                    ->where('address', 'LIKE', '%' . $data[3] . '%')
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('phone', $data[2])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[2])
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', 'LIKE', $data[1] . '%')
                                    ->where('address', 'LIKE', '%' . $data[3] . '%')
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('phone', $data[2])
                                    ->where('address', 'LIKE', '%' . $data[3] . '%')
                                    ->where('zip', $data[6])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[1], $data[4], $data[5]);

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                $facility->name = $data[1];
                $facility->address = $data[3];
                $facility->city = $data[4];
                $facility->state = $data[5];
                $facility->zip = $data[6];
                $facility->phone = $data[2];
                $facility->county = $data[10];

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

            if ($facility->approved <= 0) {
                $facility->approved = 1;
            }

            $facility->type = $data[9];
            $facility->is_center = ($facility->type == "Day Care Center") ? 1 : 0;
            $facility->state_id = $data[0];
            $facility->operation_id = $data[0];
            $facility->capacity = $data[11];

            if ($facility->type == "Group Day Care Home") {
                $facility->approved = 2;
            }

            if ($data[12] > 0 && preg_match("/Night Care/i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Night Care Available; ";
            }

            $facility->district_office = "Department of Children & Family Services";
            $facility->do_phone = "(877) 746-0829";

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[7] <> "" || $data[8] <> "" ) {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[7] <> "" ) {
                    $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[7]));
                }

                if ($data[8] <> "") {
                    $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[8]));
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