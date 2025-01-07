<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChildcareUpdatesCommand extends Command
{
    protected $signature = 'custom:childcare-updates {filename : The name of the CSV file}';
    protected $description = 'Update childcares from CSV file';

    public function handle()
    {
        $filename = base_path('datafiles/researched/' . $this->argument('filename') . '.csv');

        if (!file_exists($filename)) {
            $this->error("Unable to open the data file '{$filename}'");
            return;
        }

        $handle = fopen($filename, 'r');

        if ($handle === false) {
            $this->error("Unable to open the data file '{$filename}'");
            return;
        }

        while (($data = fgetcsv($handle, 3000, ";")) !== false) {
            $facility = DB::table('facility')->where('id', $data[0])->first();

            if ($facility) {
                $this->info("found record id = " . $data[0]);

                if ($facility->address != trim($data[2])) {
                    $facility->address = trim($data[2]);
                    $facility->lat = 0;
                    $facility->lng = 0;
                }

                $facility->phone = trim($data[7]);

                if ($data[9] !== "") {
                    $facility->email = trim($data[9]);
                }

                if ($data[10] !== "") {
                    $facility->introduction = trim(str_replace("\n", "<br/>\n", $data[10]));
                    $facility->created_date = now()->format('Y-m-d h:i:s');
                }

                $facility->logo = trim($data[22]);

                if (preg_match("/^(http|https):/i", $data[8])) {
                    $facility->website = $data[8];
                } else if ($data[8] !== "") {
                    $facility->website = "http://" . $data[8];
                }
                
                if (preg_match("/^(http|https):/i", $data[11])) {
                    $facility->location_url = $data[11];
                } else if ($data[11] !== "") {
                    $facility->location_url = "http://" . $data[11];
                }
                
                if (preg_match("/^(http|https):/i", $data[12])) {
                    $facility->facebook_url = $data[12];
                } else if ($data[12] !== "") {
                    $facility->facebook_url = "http://" . $data[12];
                }
                
                if (preg_match("/^(http|https):/i", $data[13])) {
                    $facility->career_url = $data[13];
                } else if ($data[13] !== "") {
                    $facility->career_url = "http://" . $data[13];
                }
                
                if (preg_match("/^(http|https):/i", $data[14])) {
                    $facility->application_url = $data[14];
                } else if ($data[14] !== "") {
                    $facility->application_url = "http://" . $data[14];
                }
                
                if (preg_match("/^(http|https):/i", $data[15])) {
                    $facility->parent_handbook_url = $data[15];
                } else if ($data[15] !== "") {
                    $facility->parent_handbook_url = "http://" . $data[15];
                }
                
                if(preg_match( "/x/i",$data[16])) {
                    $facility->is_infant = 1;
                }

                if(preg_match( "/x/i",$data[17])) {
                    $facility->is_toddler = 1;
                }

                if(preg_match( "/x/i",$data[18])) {
                    $facility->is_preschool = 1;
                }

                if(preg_match( "/x/i",$data[19])) {
                    $facility->is_prek = 1;
                }

                if(preg_match( "/x/i",$data[20])) {
                    $facility->is_afterschool = 1;
                }

                if(preg_match( "/x/i",$data[21])) {
                    $facility->is_camp= 1;
                }

                if ($data[30] <> '') {
                    $facility->approved = -1;
                    $facility->status = $data[30];
                }
                
                $facility->ludate = now()->format('Y-m-d H:s:i');

                DB::table('facility')->where('id', $facility->id)->update((array) $facility);

                $facilityHour = DB::table('facilityhours')->where('facility_id', $facility->id)->first();

                if (!$facilityHour) {
                    $facilityHour = new \stdClass();
                    $facilityHour->facility_id = $facility->id;
                }

                if ($data[23] <> '') {
                    $facilityHour->monday = $data[23];
                }

                if ($data[24] <> '') {
                    $facilityHour->tuesday = $data[24];
                }

                if ($data[25] <> '') {
                    $facilityHour->wednesday = $data[25];
                }

                if ($data[26] <> '') {
                    $facilityHour->thursday = $data[26];
                }

                if ($data[27] <> '') {
                    $facilityHour->friday = $data[27];
                }

                if ($data[28] <> '') {
                    $facilityHour->saturday = $data[28];
                }

                if ($data[29] <> '') {
                    $facilityHour->sunday = $data[29];
                }

                if (isset($facilityHour->id)) {
                    DB::table('facilityhours')->where('id', $facilityHour->id)->update((array) $facilityHour);
                } else {
                    DB::table('facilityhours')->insert((array) $facilityHour);
                }
            }
        }

        fclose($handle);
    }
}
