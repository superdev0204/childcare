<?php

namespace App\Console\Commands\parsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;

class KentuckyStarRatingCsvParser extends Command
{
    protected $signature = 'custom:parse-kentucky-star-rating-csv';
    protected $description = 'Parse data using a specified parser';
    protected $file = '/datafiles/kentucky/star-ratings.csv';
    
    public function handle()
    {
        $filename = base_path($this->file);
        $handle = fopen($filename, 'r');

        while (($data = fgetcsv($handle, 2000, ";")) !== false) {

            $facility = Facility::where('state_id', $data[1])
                                ->where('state', 'KY')
                                ->first();
            
            if ($facility) {
                $this->info("found record id = " . $data[1]);
                
                if ($data[5] <> "") {
                    $facility->state_rating = $data[5];
                    if (preg_match("/Star/i",$facility->additionalInfo) == false) {
                        if ($facility->additionalInfo == "") {
                            $facility->additionalInfo = "Rated " . $data[5] . " Star. ";
                        } else {
                            $facility->additionalInfo = "Rated " . $data[5] . " Star. " . $facility->additionalInfo;
                        }
                    }
                }

                if ($data[7] == "Y") {
                    $facility->subsidized = 1;
                }

                $facility->save();
            }
        }

        fclose($handle);
    }
}