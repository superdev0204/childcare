<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Facilitydetail;
use App\Models\Facilityhours;
use App\Models\Cities;

class DCCsvParser extends Command
{
    protected $signature = 'custom:parse-dc-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/washington-dc/DoCChildCareNew.csv';
    protected $filename = [];
    
    public function handle()
    {
        $skipCount = 0;
        $existCount = 0;
        $newCount = 0;

        $row = 0;

        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 5000, ";")) !== false) {
            $row++;

            if ($row == 1) {
                $this->info("skip " . ++$skipCount);
                
                continue;
            }

            $this->info("test $row = " . $data[0]);

            $facility = Facility::where('state_id', trim($data[0]))
                                ->where('state', 'DC')
                                ->first();

            if (!$facility) {
                $facility = Facility::where('name', trim($data[1]))
                                    ->where('address', $data[3])
                                    ->where('zip', $data[6])
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('name', $data[1])
                                    ->where('address', $data[3])
                                    ->whereRaw('LOWER(city) = ?', ['washington'])
                                    ->first();
            }

            $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $data[7]);

            /* if (!$facility) {
                $query = $this->getQueryBuilder()
                    ->select('*')
                    ->from('facility')
                    ->where('name = ?')
                    ->andWhere("REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''), ' ', ''), '(', ''), ')', '') = ?")
                    ->andWhere('zip = ?')
                    ->setParameter(0, $data[1])
                    ->setParameter(1, $strippedPhoneNumber)
                    ->setParameter(2, $data[6]);

                $facility = $query->execute()->fetch(\PDO::FETCH_OBJ);
            }
            
            if (!$facility) {
                $query = $this->getQueryBuilder()
                    ->select('*')
                    ->from('facility')
                    ->where('name = ?')
                    ->andWhere("REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''), ' ', ''), '(', ''), ')', '') = ?")
                    ->andWhere('LOWER(city) = ?')
                    ->setParameter(0, $data[1])
                    ->setParameter(1, $strippedPhoneNumber)
                    ->setParameter(2, 'washington');

                $facility = $query->execute()->fetch(\PDO::FETCH_OBJ);
            } */
            
            if (!$facility) {
                $facility = Facility::where('address', $data[3])
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->where('state', 'DC')
                                    ->first();
            }
            
            if (!$facility) {
                $facility = Facility::where('address', 'LIKE', substr($data[3], 0, 10) . '%')
                                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', '') = ?", [$strippedPhoneNumber])
                                    ->where('zip', $data[6])
                                    ->first();
            }
            /* 
            if (!$facility) {
                $query = $this->getQueryBuilder()
                    ->select('*')
                    ->from('facility')
                    ->where('address = ?')
                    ->andWhere('zip = ?')
                    ->setParameter(0, $data[1])
                    ->setParameter(1, $data[6]);

                $facility = $query->execute()->fetch(\PDO::FETCH_OBJ);
            }
 */
            $uniqueFilename = $this->getUniqueFilename($data[1],'WASHINGTON', 'DC');
            if (!$facility) {
                $facility2 = Facility::where('filename', $uniqueFilename)->first();
                if ($facility2) {
                    $uniqueFilename .= "-1";
                }
            }

            if (!$facility) {
                $newCount++;
                $this->info("new record $newCount");

                $facility = new \stdClass();
                
                $facility->city = "WASHINGTON";
                $facility->state = "DC";
                $facility->county = "DISTRICT OF COLUMBIA";

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

                if (strlen($facility->phone) < 10 && $data[7] <> "") {
                    $facility->phone = $data[7];
                }

                if ($facility->approved == -5) {
                    $skipCount++;
                    $facility->ludate = date('Y-m-d H:i:s');
                    $facility->save();
                    continue;
                }
            }
            $facility->name = $data[1];
            $facility->address = $data[3];
            $facility->zip = $data[6];
            $facility->phone = $data[7];
            
            if ($data[31] == 'CDC') {
                $facility->type = 'Child Development Center';
            } elseif ($data[31] == 'CDH'){
                $facility->type = 'Child Development Home';
            } elseif ($data[31] == 'CDX') {
                $facility->type = 'Child Development Home Expanded';
            } else {
                $facility->type = $data[2];
            }
            $facility->email = $data[4];
            if ($data[8] <> "") {
                $facility->capacity = $data[8];
            }

            $facility->age_range = $data[26];

            if ($data[11] == 'Yes') {
                $facility->is_infant = 1;
            }

            if ($data[12] == 'Yes') {
                $facility->is_toddler = 1;
            }

            if ($data[13] == 'Yes') {
                $facility->is_preschool = 1;
            }

            if ($data[14] == 'Yes') {
                $facility->is_afterschool = 1;
            }

            $facility->is_center = ($data[31] == 'CDC') ? 1 : 0;
            $facility->approved = ($data[31] == 'CDC') !== false ? 1 : 2;

            $facility->subsidized = $data[9] == 'Yes' ? 1 : 0;
            $facility->accreditation = $data[16];
            $facility->status = $data[27];
            $facility->state_id = $data[0];
            
            if ($data[32] != "") {
                $contactName = explode(" ", $data[32], 2);
                $facility->contact_lastname = $contactName[1];
                $facility->contact_firstname = $contactName[0];
            } 
            
            if ($data[33] != 'English') {
                $facility->language = $data[33];
            }
            if ($data[38] <> "") {
                $facility->lat = $data[38];
            }
            if ($data[39] <> "") {
                $facility->lng = $data[39];
            }
            
            if ($data[35] != "This facility has no mission statement at this time." &&
                 $data[35] != "This facility does not currently participate in the Capital Quality program. Learn more about Capital Quality here." && 
                    preg_match("/Mission: /i",$facility->additionalInfo) == false) {
                $facility->additionalInfo .= "Mission: " . $data[35] . "; ";
            }
            if ($data[15] !== "Not Participating") {
            	$facility->state_rating = $data[15];
            }
            
            $facility->district_office = 'District of Columbia Child Care Licensing Unit';
            $facility->do_phone = '(202) 727-1839';

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
            $facilityDetail->initial_application_date = date('Y-m-d', strtotime($data[36]));
            $facilityDetail->current_license_begin_date = date('Y-m-d', strtotime($data[29]));
            $facilityDetail->current_license_expiration_date = date('Y-m-d', strtotime($data[30]));

            if (isset($facilityDetail->id)) {
                $facilityDetail->save();
            } else {
                DB::table('facilitydetail')->insert((array) $facilityDetail);
            }

            $facilityHour = Facilityhours::where('facility_id', $facility->id)->first();

            if (!$facilityHour) {
                $facilityHour = new \stdClass();
                $facilityHour->facility_id = $facility->id;
            }

            if ($data[19] <> "" ) {
                $facilityHour->monday = $data[19];
            }
            if ($data[20] <> "" ) {
                $facilityHour->tuesday = $data[20];
            }
            if ($data[21] <> "" ) {
                $facilityHour->wednesday = $data[21];
            }
            if ($data[22] <> "" ) {
                $facilityHour->thursday = $data[22];
            }
            if ($data[23] <> "" ) {
                $facilityHour->friday = $data[23];
            }
            if ($data[24] <> "" ) {
                $facilityHour->saturday = $data[24];
            }
            if ($data[25] <> "" ) {
                $facilityHour->sunday = $data[25];
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