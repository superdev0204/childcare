<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Response as LaravelResponse;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class RssController extends Controller
{
    public function index(Request $request)
    {
        // $feed = new \Laravelium\Sitemap\Sitemap;
        // $feed->setEncoding('UTF-8');
        // $feed->setTitle('ChildcareCenter.us');
        // $feed->setDescription('Lastest child care center and home daycare updates');
        // $feed->setLink(URL::to('/rss'));
        // $providers = Facility::getRssFacilities(50); // Assuming Facility is your Eloquent model for providers

        // foreach ($providers as $provider) {
        //     $entry = $feed->add('provider_detail/' . $provider->filename);
        //     $entry->title($provider->name)
        //         ->description($provider->city . ',' . $provider->state . ' - ' . $provider->introduction)
        //         ->lastMod($provider->created_at);
        // }

        // $response = LaravelResponse::make($feed->render('rss'), 200);
        // $response->header('Content-Type', 'application/rss+xml; charset=utf-8');
        
        // return $response;
    }
}
