<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Iptracker;
use App\Models\Facility;
use App\Models\States;
use App\Models\Cities;
use App\Models\Zipcodes;
use App\Models\Summary;
use App\Models\Reviews;
use App\Models\Pages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use ReCaptcha\ReCaptcha;


class IndexController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $states = States::where('country', 'US')
                        ->orderBy('state_name', 'asc')
                        ->get();
        
        $summary = Summary::where('id', 1)->first();
        
        $facilities = Facility::where('approved', '>=', 1)
                            ->whereRaw('CHAR_LENGTH(introduction) >= 50')
                            ->whereRaw('CHAR_LENGTH(operation_id) >= 5')
                            ->where('created_date', '>=', now()->subDays(10)->toDateString())
                            ->orderBy('created_date', 'desc')
                            ->limit(5)
                            ->get();

        Cache::remember('latest_facilities', 21600, function () use ($facilities) {
            return $facilities;
        });

        foreach ($facilities as $facility){
            $facility->phone = $this->formatPhoneNumber($facility->phone);
        }

        $reviews = Reviews::where('approved', 1)
                        ->where('rating', '>=', 4)
                        ->whereRaw('CHAR_LENGTH(comments) >= 20')
                        ->where('review_date', '>=', now()->subDays(7)->toDateString())
                        ->orderBy('review_date', 'desc')
                        ->limit(5)
                        ->get();

        Cache::remember('latest_reviews', 21600, function () use ($reviews) {
            return $reviews;
        });

        $pages = Pages::where('approved', 1)
                    ->where('domain', 'childcarecenter.us')
                    ->orderByDesc('created_date')
                    ->limit(6)
                    ->get();
        
        return view('index', compact('states', 'summary', 'facilities', 'reviews', 'pages', 'user'));
    }

    public function verify(Request $request)
    {
        $user = Auth::user();
        
        if ($request->isMethod('post')){
            $recaptcha = new ReCaptcha(env('DATA_SECRETKEY'));
            $response = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());

            if ($response->isSuccess()) {
                // reCAPTCHA verification successful, process the form submission
            }
            else{
                $request->validate(['recaptcha-token' => 'required']);
            }

            $request->validate($valid_item);

            $iptracker = Iptracker::where('ip', $_SERVER['REMOTE_ADDR'])
                                    ->whereDate('hour', date('Y-m-d'))
                                    ->get();
            
            if ($iptracker) {
                $iptracker->total_count = $iptracker->total_count + 1;

                if ($iptracker->current_count >= 200) {
                    $iptracker->current_count = 100;
                }

                if ($iptracker->zip_count >= 100) {
                    $iptracker->zip_count = 50;
                }

                $iptracker->save();
            }

            if (request()->query('backUrl')) {
                return redirect(request()->query('backUrl'));
            }

            return redirect('/');
        }
        return view('verify', compact('user', 'request'));
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $message = '';
        return view('search', compact('user', 'message'));
    }

    public function search_results(Request $request)
    {
        $user = Auth::user();
        
        $message = "";
        $withErrors = [];
        
        if($request->zip && strlen($request->zip) < 5){
            $withErrors['zip'] = 'The zip code field must be at least 5 characters.';
        }

        if($request->location && strlen($request->location) < 3){
            $withErrors['location'] = 'The city/state field must be at least 3 characters.';
        }

        // if(!$request->name){
        //     $withErrors['name'] = 'The name field is required';
        // }
        
        if($request->name && strlen($request->name) < 5){
            $withErrors['name'] = 'The name field must be at least 5 characters.';
        }

        // if(!$request->address){
        //     $withErrors['address'] = 'The address field is required';
        // }
        
        if($request->address && strlen($request->address) < 5){
            $withErrors['address'] = 'The address field must be at least 5 characters.';
        }

        // if(!$request->phone){
        //     $withErrors['phone'] = 'The phone field is required';
        // }
        
        if($request->phone && strlen($request->phone) < 10){
            $withErrors['phone'] = 'The phone field must be at least 10 characters.';
        }

        if((!$request->zip && !$request->location) || count($withErrors) > 0){
            if((!$request->zip && !$request->location)){
                $message = 'You must enter a ZIP code or City to search.';
            }
            else{
                $message = 'Please make the following corrections and submit again.';
            }
            return view('search', compact('user', 'message', 'request'))->withErrors($withErrors);
        }

        if (($request->zip || $request->location) && ($request->name || $request->address || $request->phone)) {
            $query = Facility::where('approved', 1);

            if ($request->location) {
                @list($city, $stateCode) = explode(',', $request->location);

                $city = trim($city);
                $stateCode = $stateCode ? trim($stateCode) : null;

                $query->where('city', trim($city));

                if ($stateCode) {
                    $query->where('state', trim($stateCode));
                }
            }

            if ($request->type == 'center') {
                $query->where('is_center', 1);
            } else {
                $query->where('is_center', 0);
            }

            if ($request->zip) {
                $query->where('zip', $request->zip);
            }

            if ($request->name) {
                $query->where('name', $request->name);
            }

            if ($request->address) {
                $query->where('address', $request->address);
            }

            if ($request->phone) {
                $query->where('phone', $request->phone);
            }

            $providers = $query->orderByDesc('ranking')->orderBy('name')->limit(100)->get();
        }
        elseif ($request->zip) {
            if ($request->type == 'center') {
                /** @var Zipcode $zipcode */
                $zipcode = Zipcodes::where('zipcode', $request->zip)->first();
                Cache::remember($request->zip . '_zipcode', 72000, function () use ($zipcode) {
                    return $zipcode;
                });
            } else {
                /** @var Zipcode $zipcode */
                $zipcode = Zipcodes::where('homebase_count', '>', 0)
                                    ->where('zipcode', $request->zip)
                                    ->first();
            }

            if (!$zipcode) {
                $message = 'There is no result for your selected criteria.  Please try again with different criteria.';
                return view('search', compact('user', 'message', 'request'));
            }

            if ($request->type == 'center') {
                // /(?<state>[a-z_]+)/(?<zipcode>[0-9]+)_childcare
                return redirect()->route('centercare_zipcode', ['state' => $zipcode->statefile, 'zipcode' => $zipcode->zipcode]);
            } else {
                return redirect('/' . $zipcode->statefile . '_homecare/' . $zipcode->zipcode . '_zipcode');
            }
        }
        elseif ($request->location) {
            @list($city, $stateCode) = explode(',', $request->location);

            $city = trim($city);
            $stateCode = $stateCode ? trim($stateCode) : null;

            $query = Cities::where('city', 'like', '%' . $city . '%');
            
            if ($stateCode) {
                $query->where('state', $stateCode);
            }

            if ($request->type == 'center') {
                $cities = $query->get();
            } else {
                $cities = $query->where('homebase_count', '>=', 1)->get();
            }

            if (count($cities) == 1) {
                /** @var City $city */
                $city = $cities[0];

                if ($request->type == 'center') {
                    return redirect()->route('centercare_city', ['state' => $city->statefile, 'city' => $city->filename]);
                } else {
                    return redirect('/' . $city->statefile . '_homecare/' . $city->filename . '_city');
                }
            } elseif (count($cities) > 1) {
                $message = 'Please select a city from the list below.';
                $type = $request->type;
                return view('search', compact('cities', 'type', 'user', 'message', 'request'));
            } else {
                $message = 'There is no result for your selected criteria.  Please try again with different criteria.';
                return view('search', compact('user', 'message', 'request'));
            }
        }
        
        return view('search', compact('user', 'message', 'request'));
    }

    public function about()
    {
        $user = Auth::user();
        return view('about', compact('user'));
    }

    public function faqs()
    {
        $user = Auth::user();
        return view('faqs', compact('user'));
    }

    public function privacy()
    {
        $user = Auth::user();
        return view('privacy', compact('user'));
    }

    public function wesupport()
    {
        $user = Auth::user();
        return view('wesupport', compact('user'));
    }

    public function guidelines()
    {
        $user = Auth::user();
        return view('guidelines', compact('user'));
    }

    public function formatPhoneNumber($phoneNumber)
    {
        $fphoneNumber = preg_replace("/[^0-9]/", "", $phoneNumber);

		if(strlen($fphoneNumber) == 7) {
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $fphoneNumber);
        } elseif(strlen($fphoneNumber) == 10) {
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $fphoneNumber);
        } else {
            return $phoneNumber;
        }
	}

    public function formatLogoURL($logoUrl, $providerId)
    {
        if(preg_match( "/[http|https]:\/\//i", $logoUrl)) {
            return $logoUrl;
        }
        if ($logoUrl <> "") {
            return env('IDRIVE_BITBUCKET_URL') . "/" . $logoUrl;
        } else {
            return "";
        }
	}
}
