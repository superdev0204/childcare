<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Images;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            $images = Images::where('approved', 0)
                            ->orderBy('created', 'asc')
                            ->get();

            return view('admin.image', compact('images', 'user'));
        }
        else{
            return redirect('/user/login');
        }
    }

    public function approve(Request $request)
    {
        if (!$request->isMethod('post')){

            return response()->json(['error' => true, 'data' => []], 404);
        }

        /** @var Image $image */
        $image = Images::where('id', $request->id)->first();

        if (!$image) {
            return redirect('/admin');
        }

        $image->approved = 1;

        $image->save();

        if ($image->type == 'LOGO') {
            $provider = $image->provider;
            $provider->logo = $image->imagename;

            $provider->save();
        }

        if ($request->backUrl) {
            return redirect($request->backUrl);
        }

        return redirect('/admin');
    }

    public function disapprove(Request $request)
    {
        if (!$request->isMethod('post')) {

            return response()->json(['error' => true, 'data' => []], 404);
        }

        /** @var Image $image */
        $image = Images::where('id', $request->id)->first();

        if (!$image) {
            return redirect('/admin');
        }

        $image->approved = -2;

        $image->save();

        if ($request->backUrl) {
            return redirect($request->backUrl);
        }

        return redirect('/admin');
    }

    public function delete(Request $request)
    {
        if (!$request->isMethod('post')){

            return response()->json(['error' => true, 'data' => []], 404);
        }

        /** @var Image $image */
        $image = Images::where('id', $request->id)->first();

        if (!$image) {
            return redirect('/admin');
        }

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

        if ($request->backUrl) {
            return redirect($request->backUrl);
        }

        return redirect('/admin');
    }
}
