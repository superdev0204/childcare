<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\Images;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

class MoveIdriveFromLocalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:move-image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move images from Local to Idrive';

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

        $query = Facility::select('id', 'logo')
                        ->where("approved", ">=", 0)
                        ->where("logo", "<>", "")
                        ->where(function ($query) {
                            $query->where('logo', 'not like', 'http://%')
                                ->Where('logo', 'not like', 'https://%');
                        })
                        ->where("state", "<>", "");

        $providers = $query->get();

        $no = 0;
        foreach ($providers as $provider) {
            $no++;
            $this->info("id: $provider->id, count: $no");

            $path = public_path() . "/images/providers/" . substr($provider->id, -1) . "/" . $provider->id . "/" . $provider->logo;
            $filename = $provider->id . "_" . $provider->logo;

            try {
                if (file_exists($path)) {
                    // Open the file
                    $fileHandle = fopen($path, 'r');
        
                    // Check if the file was opened successfully
                    if ($fileHandle) {
                        // Upload the file to S3
                        $result = $s3->putObject([
                            'Bucket' => env('IDRIVE_BITBUCKET_NAME'),
                            'Key'    => $filename,
                            'Body'   => $fileHandle, // Pass the file handle directly
                        ]);
        
                        // Close the file handle
                        fclose($fileHandle);
        
                        // Update the provider object and save it
                        $provider->logo = $filename;
                        $provider->save();
                        $this->info($provider->id . "|" . $filename);
                    } else {
                        $this->info("Failed to open file: $path");
                    }
                } else {
                    $this->info("The $path file does not exist.");
                }
            } catch (AwsException $e) {
                $provider->logo = '';
                $provider->save();
                $this->info("Error: {$e->getMessage()}" . PHP_EOL);
            }
        }

        $query = Images::where("approved", 1)
                        ->where("imagename", "<>", "")
                        ->where(function ($query) {
                            $query->where('imagename', 'not like', 'http://%')
                                ->Where('imagename', 'not like', 'https://%');
                        });

        $images = $query->get();

        $no = 0;
        foreach ($images as $image) {
            $no++;
            $this->info("id: $image->id, count: $no");

            $path = public_path() . "/images/providers/" . substr($image->provider_id, -1) . "/" . $image->provider_id . "/" . $image->imagename;
            
            if($image->type == 'CENTER'){
                $filename = $image->provider_id . "_image_" . $image->imagename;
            }
            else{
                $filename = $image->provider_id . "_" . $image->imagename;
            }

            try {
                if (file_exists($path)) {
                    // Open the file
                    $fileHandle = fopen($path, 'r');

                    $result = $s3->putObject([
                        'Bucket' => env('IDRIVE_BITBUCKET_NAME'),
                        'Key'    => $filename,
                        'Body'   => $fileHandle, // Pass the file handle directly
                    ]);

                    // Close the file handle
                    fclose($fileHandle);

                    $image->imagename = $filename;
                    $image->save();
                    $this->info($image->id . "|" . $filename);                
                }
                else{
                    $this->info("The $path file does not exist.");
                }
            } catch (AwsException $e) {
                // $image->imagename = "";
                $image->delete();
                $this->info("Error: {$e->getMessage()}" . PHP_EOL);
            }
        }
    }
}
