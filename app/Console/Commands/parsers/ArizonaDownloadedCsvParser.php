<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Cities;

class ArizonaDownloadedCsvParser extends Command
{
    protected $signature = 'custom:parse-arizona-downloaded-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/arizona/arizona2.csv';
    protected $filename = [];

    public function handle()
    {
        $existCount = 0;
        $newCount = 0;
        $skipCount = 0;

        $row = 0;

        while (($data = fgetcsv($this->handle, 2000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                continue;
            }

            $this->info("test $row = " . $data[3]);

            if ($data[1] == "CHILD CARE SMALL GROUP HOME") {
                $address = explode(" ", $data[4], 2);
                if ($address[1] != "") {
                    $data[4] = $address[1];
                }
            }

            $facility = Facility::where('state_id', trim($data[13]))
                                ->where('state', 'AZ')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('operation_id', trim($data[5]))
                                    ->where('state', 'AZ')
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[3])
                                    ->where('phone', $data[10])
                                    ->first();
            }

            if (!$facility) {
                # Check by state license number
                $facility = Facility::where('name', $data[3])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[2])
                                    ->first();
            }

            if (!$facility) {
                # Check by state license number
                $facility = Facility::where('phone', $data[10])
                                    ->where('address', $data[4])
                                    ->where('zip', $data[2])
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[3], $data[8], 'az');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();

                $facility->name = trim($data[3]);
                $facility->address = trim($data[4]);
                $facility->zip = trim($data[2]);
                $facility->city = trim($data[8]);
                $facility->county = trim($data[9]);
                $facility->phone = trim($data[10]);
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
                $this->info("existing $existCount " . $facility->name . " | " . $data[4]);

                if ($facility->approved == -5) {
                    $skipCount++;;
                    continue;
                }
            }
            if ($data[12] <> "") {
            	$facility->capacity = trim($data[12]);
            }
            $facility->type = trim($data[1]);
            $facility->state_id = trim($data[13]);
            $facility->operation_id = trim($data[5]);
            $facility->email = trim($data[17]);
            
            if ($facility->type == "CHILD CARE SMALL GROUP HOME") {
                $facility->is_center = 0;
                if ($facility->capacity>=10) {
                    $facility->approved = 2;
                    if ($data[14]<> "") {
                        $facility->lng=$data[14];
                    }
                    if ($data[15]<> "") {
                        $facility->lat=$data[15];
                    }
                }
            } else {
                $facility->is_center = 1;
            }
            if ($facility->age_range == "") {
				if ($data[20] <> "N/A") {
					$facility->age_range = 'Infant; ';
				}
				if ($data[21] <> "N/A") {
					$facility->age_range .= 'Ones; ';
				}
				if ($data[22] <> "N/A") {
					$facility->age_range .= 'Twos; ';
				}
				if ($data[23] <> "N/A") {
					$facility->age_range .= 'Three to Five; ';
				}
				if ($data[24] <> "N/A") {
					$facility->age_range .= 'School-Age';
				}
            }
            
            $facility->district_office = "ADHS Division of Licensing Services";
            $facility->do_phone = "(602) 364-2539";

            if (isset($facility->id)) {
                $facility->ludate = date('Y-m-d H:i:s');
                $facility->save();
            } else {
                $facility->created_date = $facility->ludate = date('Y-m-d H:i:s');
                $facility = DB::table('facility')->insert((array) $facility);
            }

            if ($data[6] <> "" || $data[7] <> "" || $data[11] <> "") {
                $facilityDetail = Facilitydetail::where('facility_id', $facility->id)->first();

                if (!$facilityDetail) {
                    $facilityDetail = new \stdClass();
                    $facilityDetail->facility_id = $facility->id;
                }

                if ($data[6] <> "") {
                    $expDates = explode("/", $data[6], 3);
                    $facilityDetail->current_license_begin_date = $expDates[2] . "-" . $expDates[0] . "-" . $expDates[1];
                }

                if ($data[7] <> "") {
                    $expDates = explode("/", $data[7], 3);
                    $facilityDetail->current_license_expiration_date = $expDates[2] . "-" . $expDates[0] . "-" . $expDates[1];
                }

                if ($data[11] <> "(   )   -") {
                    $facilityDetail->fax = $data[11];
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