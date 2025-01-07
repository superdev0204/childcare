<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Helper\ProgressBar;
use App\Models\Facility;

class ProcessBrokenLinkFileCommand extends Command
{
    protected $signature = 'custom:process-broken-link-file {filename : The name of the CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update website from CSV file';

    public function handle()
    {
        $filename = base_path('datafiles/broken-links/' . $this->argument('filename') . '.csv');

        $invalidDataFilename = base_path('datafiles/broken-links/' . $this->argument('filename') . '.error.csv');

        if (!File::exists($filename)) {
            $this->error('Unable to open the data file');
            return;
        }

        $invalidDataFile = fopen($invalidDataFilename, 'w');

        $row = 0;
        $skipCount = 0;
        $errorCount = 0;
        $updatedCount = 0;

        $progressBar = $this->output->createProgressBar(count(File::lines($filename)) - 1);
        
        $handle = fopen($filename, 'r');
        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            $row++;

            $progressBar->advance();

            if ($row == 1) {
                $progressBar->start();
                fwrite($invalidDataFile, join(";", $data) . "\n");

                continue;
            }

            if (!$data[4]) {
                #$skipCount++;
                #continue;
            }

            $url = $data[0];
            if (strrpos($url, '/')> 0) {
	            $filename = substr($url, strrpos($url, '/') + 1);
            } else {
            	$filename = $url;
            }

            $facility = Facility::where('filename', $filename)->first();

            if (!$facility) {
                $skipCount++;
                print "skip $filename \n";
                continue;
            }

            $facility->website = $data[4];

            try {
                $facility->save();
                $updatedCount++;
            } catch (\Exception $exception) {
                fwrite($invalidDataFile, join(";", $data) . "\n");
                $errorCount++;
                $this->error($exception->getMessage());
            }            
        }

        if (count(File::lines($invalidDataFilename)) == 1) {
            @unlink($invalidDataFilename);
        }

        $progressBar->finish();

        $this->info("\n$updatedCount updated; $skipCount skipped; $errorCount errors");
    }
}