<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Facility;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

class UpdateLogoFromUrlCommand extends Command
{
    protected $signature = 'update-logo-from-url {--limit=} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download logo of facility table from orignal url to local and then upload to Idrive.';

    public function handle()
    {
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

        $query = Facility::query()
            // ->where('approved', 1)
            ->where('logo', '!=', '')
            ->whereNull('image_statuscode')
            ->where(function ($query) {
                $query->where('logo', 'like', 'http://%')
                      ->orWhere('logo', 'like', 'https://%');
            })
            ->where('state', '!=', '');

        if ($this->option('id')) {
            $query->where('id', $this->option('id'));
        }

        if ($this->option('limit')) {
            $query->limit($this->option('limit'));
        }

        $providers = $query->get();

        foreach ($providers as $provider) {

            $this->info($provider->id);
            
            $imageUrl = $provider->logo;

            $ch = curl_init($imageUrl);
            curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch,CURLOPT_TIMEOUT, 15);
            $content = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $provider->image_statuscode = $httpCode;
            $provider->save();
            
            if ($httpCode != 200) {
                $this->error("error: Http Status Code - " . $httpCode . "|" . $provider->id . "|" . $imageUrl);
                if($httpCode == 404){
                    $provider->logo = "";
                    $provider->save();
                }
                continue;
            }

            $directory = public_path("images/providers/" . substr($provider->id, -1) . "/" . $provider->id);

            if (file_exists($directory) == false) {
                mkdir($directory, 0755, true);
            }

            $filename = basename($imageUrl);

            $filename = str_replace(["%20", "%28", "%29", "%2", "%"], "-", $filename);

            if (preg_match("/\?/",$filename)) {
                $filearray = explode("?", $filename, 2);
                $filename = $filearray[0];
            }

            if (preg_match("/&/",$filename)) {
                $filearray = explode("&", $filename, 2);
                $filename = $filearray[0];
            }

            if (preg_match("/\.[jpg|gif|png|webp|svg]/i",$filename) == false) {
                $filename = $provider->id . ".jpg";
            } else {
                if (strlen($filename) > 65) {
                    $filename = substr($filename, -65);
                }
                $filename = $provider->id . "_" . $filename;
            }

            $filename = "logo-" . $filename;
            $path = $directory . "/" . $filename;

            file_put_contents($path, $content);

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($path);

            $size = getimagesize($path);

            if (($mimeType != 'image/webp' && $mimeType != 'image/svg+xml') && !$size) {
                $this->error("error: Cannot get image size " . $provider->id . "|" . $imageUrl);
                // @unlink($path);
                // $provider->logo = "";
                // $provider->save();
                continue;
            }

            // $provider->logo = $filename;
            // $provider->save();

            try {
                $result = $s3->putObject([
                    'Bucket' => env('IDRIVE_BITBUCKET_NAME'),
                    'Key'    => $filename,
                    'SourceFile' => $path,
                ]);

                $provider->logo = $filename;
                $this->info($provider->id . "|" . $imageUrl . "|" . $filename);
            } catch (AwsException $e) {
                $provider->logo = "";
                $this->info("Error: {$e->getMessage()}" . PHP_EOL);
            }

            $provider->save();
            File::delete($path);

            $this->info($provider->id . "|" . $imageUrl . "|" . $filename);
        }
    }
}
