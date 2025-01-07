<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Cities;

class Illinois2CsvParser extends Command
{
    protected $signature = 'custom:parse-illinois2-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/illinois/DaycareProviders.csv';
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

            if ($row < 2) {
                continue;
            }

            $this->info("test $row = " . $data[1]);
            
            if ($data[6] == "DCH" && $data[1]<> "") {
            	$straddress = explode(" ", $data[1], 2);
            	if ($straddress[1] != "") {
            		$data[1] = $straddress[1];
            	}
            }
            
            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[5]);

            $facility = Facility::where('state_id', $data[13])
                                ->where('state', 'IL')
                                ->first();
            
            if (!$facility) {
                $facility = Facility::where('operation_id', $data[13])
                                    ->where('state', 'IL')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::whereRaw('LOWER(name) = ?', [strtolower($data[0])])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->where('address', $data[1])
                                    ->where('zip', $data[4])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', 'LIKE', $data[0] . '%')
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->where('address', 'LIKE', '%' . $data[1] . '%')
                                    ->where('zip', $data[4])
                                    ->first();
            }

            $address = str_replace(['street', 'avenue', 'drive', 'road', '.'], ['st', 'ave', 'dr', 'rd', ''], strtolower($data[1]));

            if (!$facility) {
                $facility = Facility::whereRaw('LOWER(name) = ?', [strtolower($data[0])])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", [$address])
                                    ->where('zip', $data[4])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', $data[0])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", [$address])
                                    ->where('zip', $data[4])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::where('name', 'LIKE', $data[0] . '%')
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", [$address])
                                    ->where('zip', $data[4])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(address), 'avenue', 'ave'), 'street', 'st'), 'drive', 'dr'), 'road', 'rd'), '.', '') LIKE ?", [$address])
                                    ->where('zip', $data[4])
                                    ->first();
            }

            if (!$facility) {
                $facility = Facility::whereRaw('LOWER(name) = ?', [strtolower($data[0])])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->where('state', 'IL')
                                    ->first();
            }

            if (!$facility) {
                $uniqueFilename = $this->getUniqueFilename($data[0], $data[2], 'il');

                $facility = Facility::where('filename', $uniqueFilename)->first();
            }

            if (!$facility) {
            	
                if (strpos(strtolower($data[7]), strtolower('REVOKE LICENSE')) !== false ||
            		strpos(strtolower($data[7]), strtolower('REFUSE TO RENEW')) !== false ||
                    strpos(strtolower($data[7]), strtolower('SURRENDERED WITH CAUSE')) !== false ||
                    strpos(strtolower($data[7]), strtolower('SURRENDERED UNDER INVESTIGATION')) !== false ||
                    strpos(strtolower($data[7]), strtolower('PENDING REVOCATION')) !== false ) {
            				$skipCount++;
            				continue;
            			}
            			
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                
                $facility->city = $data[2];
                $facility->state = 'IL';
                $facility->county = $data[3];

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
                $this->info("existing $existCount " . $facility->name . " | " . $data[0]);

                if ($facility->approved == -5) {
                    $skipCount++;
                    continue;
                }
            }
            $facility->name = $data[0];
            $facility->address = $data[1];
            $facility->zip = $data[4];
            $facility->phone = $data[5];
            $facility->state_id = $data[13];
            $facility->operation_id = $data[13];
            
            if ($facility->approved <= 0) {
                $facility->approved = 1;
            }
            if (strpos(strtolower($data[7]), strtolower('REVOKE LICENSE')) !== false || 
                strpos(strtolower($data[7]), strtolower('REFUSE TO RENEW')) !== false ||
                strpos(strtolower($data[7]), strtolower('SURRENDERED WITH CAUSE')) !== false) {
            	$facility->approved = -1;
            }
            
            $facility->is_center = ($data[6] == "DCC") ? 1 : 0;
            
            if ($data[6] == 'DCC') {
                $facility->type = 'Day Care Center';
            } elseif ($data[6] = 'DCH') {
                $facility->type = 'Day Care Home';
            } elseif ($data[6] = 'GDC') {
                $facility->type = 'Group Home Day Care';
            } else {
                $facility->type = $data[6];
            }


            $facility->status = $data[7];
            $facility->capacity = $data[10];
            $facility->age_range = $data[8];

            $language = ucfirst(trim(str_replace('english', '', strtolower($data[12])), ' ,'));

            if ($language) {
                $facility->language = $language;
            }

            if (!$facility->is_center) {
                $facility->approved = 2;
            }

            if ($data[11] > 0 && preg_match("/Night Care/i",$facility->additionalInfo) == false) {
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