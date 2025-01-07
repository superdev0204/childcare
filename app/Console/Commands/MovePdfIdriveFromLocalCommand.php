<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Inspections;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

class MovePdfIdriveFromLocalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:move-inspection {state : The state code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move inspections from Local to Idrive';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create an S3 client for IDrive e2           
        $profile_credentials = [
            "endpoint" => env("IDRIVE_E2_ENDPOINT"),
            "region" => env('IDRIVE_BITBUCKET_REGION'),
            "version" => "latest",
            'credentials' => [
                    'key' => env('IDRIVE_ACCESS_KEY'),
                    'secret' => env('IDRIVE_SECRET_KEY'),
            ]
        ];
        $s3 = S3Client::factory($profile_credentials);

        $state = $this->argument('state');
        $path = public_path('inspections/' . $state);

        if (file_exists($path)) {
            $files = File::allFiles($path);

            // Loop through each file and upload it to IDrive
            $no = 0;
            foreach ($files as $file) {
                $no++;
                $filePath = $state . '_' . $file->getFilename();
                $fileContents = File::get($file->getRealPath());

                $this->info("count: $no, filename: $filePath");

                $inspection = Inspections::where('state', strtoupper($state))
                                        ->where('report_url', $file->getFilename())
                                        ->first();

                // // Upload to IDrive
                // Storage::disk('idrive')->put($filePath, $fileContents);

                if($inspection){
                    try {
                        // Upload the file to S3
                        $result = $s3->putObject([
                            'Bucket' => env('IDRIVE_BITBUCKET_NAME1'),
                            'Key'    => $filePath,
                            'Body'   => $fileContents, // Pass the file handle directly
                        ]);
                        $this->info("Uploaded: $filePath");
                        
                        $inspection->report_url = $filePath;
                        $inspection->save();
                    } catch (AwsException $e) {
                        $this->info("Error: {$e->getMessage()}" . PHP_EOL);
                    }
                }
                else{
                    $this->info("Already Uploaded: $filePath");
                }
            }
        }
        else{
            $this->error("The folder doesn't exist");
        }
    }
}
