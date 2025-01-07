<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use ReCaptcha\ReCaptcha;
use Illuminate\Support\Facades\Auth;
use App\Models\Facility;
use App\Models\Images;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $user = Auth::user();
        
        $message = "";
        $method = $request->method();
        
        if (!$user) {
            return redirect('/provider');
        }

        $providerId = $request->pid;

        if (!$providerId) {
            /** @var Facility $provider */
            $provider = $user->provider;
        } else {
            /** @var Facility $provider */
            $provider = Facility::where('id', $providerId)->first();
        }

        if (!$provider) {
            return redirect('/provider');
        }

        $images = Images::where('provider_id', $provider->id)->get();
        $imageCount = 0;
        foreach ($images as $image) {
            if ($image->type != "LOGO") {
              $imageCount++;
            }
        }

        if ($imageCount >= 12) {
            $message = 'You cannot upload anymore pictures at this point.';
            return view('provider.upload', compact('user', 'provider', 'message', 'images', 'imageCount'));
        }
        
        // $imagePath = public_path() . '/images/providers/' . substr($provider->id, -1) . '/' . $provider->id;

        // if (!file_exists($imagePath)) {
        //     mkdir($imagePath, 0755, true);
        // }

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

        if ($method == "POST") {
            $validated = $request->validate([
                'logo' => 'file|max:' . (env('UPLOAD_MAX_FILESIZE') * 1024),
                'image1' => 'file|max:' . (env('UPLOAD_MAX_FILESIZE') * 1024),
                'image2' => 'file|max:' . (env('UPLOAD_MAX_FILESIZE') * 1024),
            ]);

            $logo_image = $request->file('logo');
            $image1 = $request->file('image1');
            $image2 = $request->file('image2');

            if(!empty($logo_image)){
                $imageName = $providerId . '_logo_' . $logo_image->getClientOriginalName();
                // $path = $logo_image->move($imagePath . '/' , $imageName);

                // Calculate the MD5 checksum of the file
                $contentMD5 = base64_encode(md5_file($logo_image->getRealPath(), true));

                try {
                    $result = $s3->putObject([
                        'Bucket' => env('IDRIVE_BITBUCKET_NAME'),
                        'Key'    => $imageName,
                        'SourceFile' => $logo_image->getRealPath(),
                        'ContentMD5' => $contentMD5,
                    ]);

                    $image = Images::create([
                        'provider_id' => $providerId,
                        'type' => 'LOGO',
                        'imagename' => $imageName,
                        'approved' => 0,
                        'altname' => '',
                        'image_url' => '',
                        'path' => '',
                    ]);
                } catch (AwsException $e) {
                    return "Error: {$e->getMessage()}" . PHP_EOL;
                }
            }

            if(!empty($image1)){
                if ($imageCount >= 12) {
                    $message = 'You cannot upload anymore pictures at this point.';
                    return view('provider.upload', compact('user', 'provider', 'images', 'imageCount', 'message', 'request'));
                }
                else{
                    $imageName = $providerId . "_" . $image1->getClientOriginalName();
                    // $path = $image1->move($imagePath . '/' , $imageName);

                    // Calculate the MD5 checksum of the file
                    $contentMD5 = base64_encode(md5_file($image1->getRealPath(), true));

                    try {
                        $result = $s3->putObject([
                            'Bucket' => env('IDRIVE_BITBUCKET_NAME'),
                            'Key'    => $imageName,
                            'SourceFile' => $image1->getRealPath(),
                            'ContentMD5' => $contentMD5,
                        ]);
    
                        $image = Images::create([
                            'provider_id' => $providerId,
                            'type' => 'CENTER',
                            'imagename' => $imageName,
                            'approved' => 0,
                            'altname' => ($request->image1Alt) ? $request->image1Alt : '',
                            'image_url' => '',
                            'path' => '',
                        ]);
    
                        $imageCount++;
                    } catch (AwsException $e) {
                        return "Error: {$e->getMessage()}" . PHP_EOL;
                    }
                }
            }

            if(!empty($image2)){
                if ($imageCount >= 12) {
                    $message = 'You cannot upload anymore pictures at this point.';
                    return view('provider.upload', compact('user', 'provider', 'images', 'imageCount', 'message', 'request'));
                }
                else{
                    $imageName = $providerId . "_" . $image2->getClientOriginalName();
                    // $path = $image2->move($imagePath . '/' , $imageName);

                    // Calculate the MD5 checksum of the file
                    $contentMD5 = base64_encode(md5_file($image2->getRealPath(), true));

                    try {
                        $result = $s3->putObject([
                            'Bucket' => env('IDRIVE_BITBUCKET_NAME'),
                            'Key'    => $imageName,
                            'SourceFile' => $image2->getRealPath(),
                            'ContentMD5' => $contentMD5,
                        ]);
    
                        $image = Images::create([
                            'provider_id' => $providerId,
                            'type' => 'CENTER',
                            'imagename' => $imageName,
                            'approved' => 0,
                            'altname' => ($request->image2Alt) ? $request->image2Alt : '',
                            'image_url' => '',
                            'path' => '',
                        ]);
    
                        $imageCount++;
                    } catch (AwsException $e) {
                        return "Error: {$e->getMessage()}" . PHP_EOL;
                    }
                }
            }

            if(empty($logo_image) && empty($image1) && empty($image2)){
                $message = 'Please make the following corrections and submit again.';
            }
        }

        $images = Images::where('provider_id', $provider->id)->get();

        return view('provider.upload', compact('user', 'provider', 'images', 'imageCount', 'message', 'request'));
    }

    public function delete(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/provider');
        }

        $imageId = $request->id;
        $providerId = $request->pid;

        if (!$imageId || !$providerId) {
            return redirect('/');
        }

        /** @var Image $image */
        $image = Images::where('id', $imageId)
                        ->where('provider_id', $providerId)
                        ->first();
        
        if (!$image) {
            return redirect('/');
        }

        // $imagePath = public_path() . '/images/providers/' . substr($image->provider_id, -1) . '/' . $image->provider_id;
        // @unlink($imagePath . '/' . $image->imagename);

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
        
        try {
            $result = $s3->deleteObject([
                'Bucket' => env('IDRIVE_BITBUCKET_NAME'),
                'Key'    => $image->imagename,
            ]);
            $image->delete();
        } catch (AwsException $e) {
            return "Error: {$e->getMessage()}" . PHP_EOL;
        }

        return redirect('/provider/imageupload?pid=' . $image->provider_id);
    }
}
