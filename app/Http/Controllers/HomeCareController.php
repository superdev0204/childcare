<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use ReCaptcha\ReCaptcha;
use App\Models\Facility;
use App\Models\Reviews;
use App\Models\Pages;
use App\Models\Summary;
use App\Models\States;
use App\Models\Counties;
use App\Models\Cities;
use App\Models\Zipcodes;
use App\Models\Referalresources;
use App\Models\Questions;
use App\Models\Answers;
use App\Services\BadWordService;

class HomeCareController extends Controller
{
    protected $badWordService;

    public function __construct(BadWordService $badWordService)
    {
        $this->badWordService = $badWordService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $states = States::where('country', 'US')->orderBy('state_name', 'asc')->get();

        foreach($states as $state){
            $state->state_code_lower = $this->formatCountyText($state->state_code, 'lower');
            $state->state_code_normal = $this->formatCountyText($state->state_code, 'normal');
            $state->state_code_plural = $this->formatCountyText($state->state_code, 'plural');
        }

        Cache::remember(strtolower('US') . '_states', 72000, function () use ($states) {
            return $states;
        });

        $summary = Summary::where('id', 1)->first();

        $cities = Cities::where('center_visits', '>', 10000)
                        ->orderBy('center_visits', 'desc')
                        ->limit(10)
                        ->get();

        Cache::remember('top_city_center', 72000, function () use ($cities) {
            return $cities;
        });

        $providers = Facility::where('is_center', 0)
                            ->where('approved', 2)
                            ->whereRaw('LENGTH(introduction) >= 50')
                            ->whereRaw('LENGTH(operation_id) >= 3')
                            ->whereDate('created_date', '>=', Carbon::now()->subDays(7))
                            ->orderBy('created_date', 'desc')
                            ->limit(10)
                            ->get();

        $i=0;
        foreach($providers as $provider){
            $i++;
            $provider->logo = $this->formatLogoURL($provider->logo, $provider->id, $i % 9);
            $provider->formatPhone = $this->formatPhoneNumber($provider->phone);
            $provider->momtrusted_phone = $this->formatPhoneNumber(optional(optional($provider)->detail)->momtrusted_phone);
            $provider->introduction = $this->formatProviderDescription($provider);
        }

        Cache::remember('latest_homedaycares', 21600, function () use ($providers) {
            return $providers;
        });
        
        $resources = Pages::where('categories', 'like', '%1002%')
                            ->where('approved', 1)
                            ->orderBy('created_date', 'desc')
                            ->limit(3)
                            ->get();

        return view('homecare.homecare', compact('user', 'providers', 'states', 'summary', 'cities', 'resources'));
    }
    
    public function state(Request $request)
    {
        $user = Auth::user();

        $statefile = request()->route()->parameter('state');
        
        $state = States::where('statefile', $statefile)->first();

        $state->state_code_lower = $this->formatCountyText($state->state_code, 'lower');
        $state->state_code_normal = $this->formatCountyText($state->state_code, 'normal');
        $state->state_code_plural = $this->formatCountyText($state->state_code, 'plural');

        Cache::remember($statefile . '_state', 72000, function () use ($state) {
            return $state;
        });

        if (!$state) {
            return redirect('/');
        }

        $parsedUrl = parse_url(url()->current());
        // Get the path and query
        $page_url = $parsedUrl['path'] . (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');
        $questions = Questions::where(function($query) use ($page_url) {
                                $query->where('facility_id', 0)
                                    ->orWhere('page_url', $page_url);
                            })
                            ->where('approved', '1')
                            ->orderBy('created_at', 'desc')
                            ->get();
        foreach ($questions as $question) {
            $answers = Answers::where('question_id', $question->id)
                                ->where('page_url', $page_url)
                                ->where('approved', '1')
                                ->orderBy('created_at', 'desc')
                                ->get();
            if( !empty($answers) ){
                foreach ($answers as $answer) {
                    $answer->answer_by = $this->badWordService->maskBadWords($answer->answer_by);
                    $answer->answer = $this->badWordService->maskBadWords($answer->answer);
                }
                $question->answers = $answers;
            }
            else{
                $question->answers = [];
            }

            $created = Carbon::createFromFormat('Y-m-d H:i:s', $question->created_at);
            $now = Carbon::now();
            $interval = $now->diff($created);

            $question->passed = $this->formatInterval($interval);
            $question->question_by = $this->badWordService->maskBadWords($question->question_by);
            $question->question = $this->badWordService->maskBadWords($question->question);
        }

        if ($state->nextlevel == 'DETAIL') {
            $providers = Facility::where('is_center', 1)
                                ->where('approved', '>=', 1)
                                ->where('state', $state->state_code)
                                ->where('county', $state->state_name)
                                ->orderBy('ranking', 'desc')
                                ->orderBy('name', 'asc')
                                ->limit(20)
                                ->get();

            $i=0;
            foreach($providers as $provider){
                $i++;
                $provider->logo = $this->formatLogoURL($provider->logo, $provider->id, $i % 9);
                $provider->formatPhone = $this->formatPhoneNumber($provider->phone);
                $provider->momtrusted_phone = $this->formatPhoneNumber(optional(optional($provider)->detail)->momtrusted_phone);
                $provider->introduction = $this->formatProviderDescription($provider);
            }

            $filename = $state->state_code . "_" . $state->state_name . "_homes";
            $filename = str_replace([" ","-"],"_",$filename);
            $filename = str_replace(["'","."],"",$filename);

            Cache::remember($filename, 21600, function () use ($providers) {
                return $providers;
            });

            $zipcodes = Zipcodes::where('state', $state->state_code)
                                ->orderBy('zipcode', 'asc')
                                ->get();

            return view('homecare.homecare_state_detail', compact('user', 'providers', 'state', 'zipcodes', 'questions', 'page_url'));
        }

        if ($state->homebase_count) {
            $minHomebaseCount = request()->query('display') == 'all' || $state->homebase_count < 20 ? 0 : 11;
            $counties = Counties::where('state', $state->state_code)
                                ->where('homebase_count', '>=', $minHomebaseCount)
                                ->orderBy('county', 'asc')
                                ->get();
        } else {
            $counties = Counties::where('state', $state->state_code)
                                ->orderBy('county', 'asc')
                                ->get();
        }

        $cities = Cities::where('state', $state->state_code)
                        ->where('homebase_count', '>=', 6)
                        ->orderBy('homebase_count', 'desc')
                        ->limit(10)
                        ->get();

        $providers = Facility::where('is_center', 0)
                            ->where('approved', 2)
                            ->where('state', $state->state_code)
                            ->whereRaw('LENGTH(introduction) >= 50')
                            ->whereRaw('created_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 360 DAY)')
                            ->orderBy('created_date', 'desc')
                            ->limit(5)
                            ->get();

        $i=0;
        foreach($providers as $provider){
            $i++;
            $provider->logo = $this->formatLogoURL($provider->logo, $provider->id, $i % 9);
            $provider->formatPhone = $this->formatPhoneNumber($provider->phone);
            $provider->momtrusted_phone = $this->formatPhoneNumber(optional(optional($provider)->detail)->momtrusted_phone);
            $provider->introduction = $this->formatProviderDescription($provider);
        }

        Cache::remember($state->state_code . '_homes', 21600, function () use ($providers) {
            return $providers;
        });

        $resources = Pages::where('categories', 'like', '%1002%')
                            ->where('approved', 1)
                            ->orderBy('created_date', 'desc')
                            ->limit(2)
                            ->get();

        if (request()->query('display') == 'all') {
            return view('homecare.homecare_state_with_all_counties', compact('user', 'providers', 'state', 'counties', 'cities', 'resources', 'questions', 'page_url'));
        }
        
        return view('homecare.homecare_state', compact('user', 'providers', 'state', 'counties', 'cities', 'resources', 'questions', 'page_url'));
    }

    public function all_cities(Request $request)
    {
        $user = Auth::user();

        $statefile = request()->route()->parameter('state');
        
        $state = States::where('statefile', $statefile)->first();

        $state->state_code_lower = $this->formatCountyText($state->state_code, 'lower');
        $state->state_code_normal = $this->formatCountyText($state->state_code, 'normal');
        $state->state_code_plural = $this->formatCountyText($state->state_code, 'plural');

        Cache::remember($statefile . '_state', 72000, function () use ($state) {
            return $state;
        });

        if (!$state) {
            return redirect('/');
        }

        $counties = Counties::where('state', $state->state_code)
                            ->where('homebase_count', '>=', 21)
                            ->orderBy('homebase_count', 'desc')
                            ->limit(10)
                            ->get();
        
        $cities = Cities::where('state', $state->state_code)
                        ->where('homebase_count', '>=', 1)
                        ->orderBy('city', 'asc')
                        ->get();

        $resources = [];

        $parsedUrl = parse_url(url()->current());
        // Get the path and query
        $page_url = $parsedUrl['path'] . (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');
        $questions = Questions::where(function($query) use ($page_url) {
                                $query->where('facility_id', 0)
                                    ->orWhere('page_url', $page_url);
                            })
                            ->where('approved', '1')
                            ->orderBy('created_at', 'desc')
                            ->get();
        foreach ($questions as $question) {
            $answers = Answers::where('question_id', $question->id)
                                ->where('page_url', $page_url)
                                ->where('approved', '1')
                                ->orderBy('created_at', 'desc')
                                ->get();
            if( !empty($answers) ){
                foreach ($answers as $answer) {
                    $answer->answer_by = $this->badWordService->maskBadWords($answer->answer_by);
                    $answer->answer = $this->badWordService->maskBadWords($answer->answer);
                }
                $question->answers = $answers;
            }
            else{
                $question->answers = [];
            }

            $created = Carbon::createFromFormat('Y-m-d H:i:s', $question->created_at);
            $now = Carbon::now();
            $interval = $now->diff($created);

            $question->passed = $this->formatInterval($interval);
            $question->question_by = $this->badWordService->maskBadWords($question->question_by);
            $question->question = $this->badWordService->maskBadWords($question->question);
        }

        return view('homecare.homecare_all_cities', compact('user', 'state', 'counties', 'cities', 'resources', 'questions', 'page_url'));
    }

    public function county(Request $request)
    {
        $user = Auth::user();

        $countyname = request()->route()->parameter('countyname');

        $topProviders = [];
        $cities = [];
        $zipcodes = [];
        $referalResources = [];

        $county = Counties::where('county_file', $countyname)->first();
        
        Cache::remember($countyname . '_county', 72000, function () use ($county) {
            return $county;
        });

        if (!$county) {
            return redirect('/');
        }

        /** @var State $state */
        $state = States::where('statefile', $county->statefile)->first();

        $state->state_code_lower = $this->formatCountyText($state->state_code, 'lower');
        $state->state_code_normal = $this->formatCountyText($state->state_code, 'normal');
        $state->state_code_plural = $this->formatCountyText($state->state_code, 'plural');

        Cache::remember($county->statefile . '_state', 72000, function () use ($state) {
            return $state;
        });

        $providers = Facility::where('is_center', 0)
                            ->where('approved', '>=', 1)
                            ->where('state', $county->state)
                            ->where('county', $county->county)
                            ->orderBy('ranking', 'desc')
                            ->orderBy('name', 'asc')
                            ->limit(30)
                            ->get();

        $i=0;
        foreach($providers as $provider){
            $i++;
            $provider->logo = $this->formatLogoURL($provider->logo, $provider->id, $i % 9);
            $provider->formatPhone = $this->formatPhoneNumber($provider->phone);
            $provider->momtrusted_phone = $this->formatPhoneNumber(optional(optional($provider)->detail)->momtrusted_phone);
            $provider->introduction = $this->formatProviderDescription($provider);
        }

        $filename = $county->state . "_" . $county->county . "_homes";
        $filename = str_replace([" ","-"],"_",$filename);
        $filename = str_replace(["'","."],"",$filename);

        Cache::remember($filename, 21600, function () use ($providers) {
            return $providers;
        });

        if ($county->provider_ids) {
            $ids = explode(',', $county->provider_ids);
            $topProviders = Facility::whereIn('id', $ids)
                                    ->where('is_center', 0)
                                    ->get();

            $i=0;
            foreach($topProviders as $provider){
                $i++;
                $provider->logo = $this->formatLogoURL($provider->logo, $provider->id, $i % 9);
                $provider->formatPhone = $this->formatPhoneNumber($provider->phone);
                $provider->momtrusted_phone = $this->formatPhoneNumber(optional(optional($provider)->detail)->momtrusted_phone);
                $provider->introduction = $this->formatProviderDescription($provider);
            }
        }

        if ($county->homebase_count >= 10) {
            $cities = Cities::where('state', $county->state)
                            ->where('county', $county->county)
                            ->where('homebase_count', '>=', 1)
                            ->orderBy('city', 'asc')
                            ->get();

            $filename = $county->state . "_" . $county->county . "_cities";
            $filename = str_replace([" ","-"],"_",$filename);
            $filename = str_replace(["'","."],"",$filename);

            Cache::remember($filename, 72000, function () use ($cities) {
                return $cities;
            });

            $zipcodes = Zipcodes::where('state', $county->state)
                                ->where('county', $county->county)
                                ->orderBy('zipcode', 'asc')
                                ->get();

            $filename = $county->state . "_" . $county->county . "_zipcodes";
            $filename = str_replace([" ","-"],"_",$filename);
            $filename = str_replace(["'","."],"",$filename);

            Cache::remember($filename, 72000, function () use ($zipcodes) {
                return $zipcodes;
            });
        }

        if ($county->referalResources) {
            $ids = explode(',', $county->referalResources);
            $referalResources = Referalresources::whereIn('id', $ids)->get();
        }

        $parsedUrl = parse_url(url()->current());
        // Get the path and query
        $page_url = $parsedUrl['path'] . (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');
        $questions = Questions::where(function($query) use ($page_url) {
                                $query->where('facility_id', 0)
                                    ->orWhere('page_url', $page_url);
                            })
                            ->where('approved', '1')
                            ->orderBy('created_at', 'desc')
                            ->get();
        foreach ($questions as $question) {
            $answers = Answers::where('question_id', $question->id)
                                ->where('page_url', $page_url)
                                ->where('approved', '1')
                                ->orderBy('created_at', 'desc')
                                ->get();
            if( !empty($answers) ){
                foreach ($answers as $answer) {
                    $answer->answer_by = $this->badWordService->maskBadWords($answer->answer_by);
                    $answer->answer = $this->badWordService->maskBadWords($answer->answer);
                }
                $question->answers = $answers;
            }
            else{
                $question->answers = [];
            }

            $created = Carbon::createFromFormat('Y-m-d H:i:s', $question->created_at);
            $now = Carbon::now();
            $interval = $now->diff($created);

            $question->passed = $this->formatInterval($interval);
            $question->question_by = $this->badWordService->maskBadWords($question->question_by);
            $question->question = $this->badWordService->maskBadWords($question->question);
        }

        return view('homecare.homecare_county', compact('user', 'state', 'county', 'providers', 'topProviders', 'referalResources', 'cities', 'zipcodes', 'questions', 'page_url'));
    }

    public function city(Request $request)
    {        
        $user = Auth::user();

        $cityname = request()->route()->parameter('city');

        $page = request()->query('page');

        $topProviders = [];

        $city = Cities::where('filename', $cityname)->first();
        
        $filename = $cityname . '_city';
        $filename = str_replace([" ","-"],"_",$filename);
        $filename = str_replace(["'","."],"",$filename);

        Cache::remember($filename, 72000, function () use ($city) {
            return $city;
        });

        if (!$city) {
            return redirect('/');
        }

        if (!$city->latitude || !$city->longitude) {
            $coordinates = $this->geocode('', $city->city, $city->state, '');

            if ($coordinates) {
                list($longitude, $latitude) = explode(',', $coordinates);

                $city->latitude = $latitude;
                $city->longitude = $longitude;

                $city->save();
            }
        }

        if ($city->center_count >= 2) {
            $city->center_visits = $city->center_visits + 1;
            $city->save();
        }

        $providers = Facility::where('is_center', 0)
                            ->where('approved', '>=', 1)
                            ->where('state', $city->state)
                            ->where('city', $city->city)
                            ->orderBy('ranking', 'desc')
                            ->orderBy('name', 'asc')
                            ->paginate(30);

        $filename = $city->state . "_" . $city->city . "_homes" . ($page ? "_page_" . ((int) $page) . '_per_page_30' : '');
        $filename = str_replace([" ","-"],"_",$filename);
        $filename = str_replace(["'","."],"",$filename);
        
        Cache::remember($filename, 21600, function () use ($providers) {
            return $providers;
        });

        if ($city->provider_ids) {
            $ids = explode(',', $city->provider_ids);
            $topProviders = Facility::whereIn('id', $ids)
                                    ->where('is_center', 0)
                                    ->get();

            $i=0;
            foreach($topProviders as $provider){
                $i++;
                $provider->logo = $this->formatLogoURL($provider->logo, $provider->id, $i % 9);
                $provider->formatPhone = $this->formatPhoneNumber($provider->phone);
                $provider->momtrusted_phone = $this->formatPhoneNumber(optional(optional($provider)->detail)->momtrusted_phone);
                $provider->introduction = $this->formatProviderDescription($provider);
            }
        }

        if ($city->homebase_count >= 20) {
            $zipcodes = $zipcodes = Zipcodes::where('state', $city->state)
                                            ->where('city', $city->city)
                                            ->where('homebase_count', '>=', 1)
                                            ->orderBy('zipcode', 'asc')
                                            ->get();
        } else {
            $zipcodes = $this->getZipcodesNearby($city->latitude, $city->longitude, 18, ['state' => $city->state, 'minHomebaseCount' => 1], 20);
        }

        $state = States::where('statefile', $city->statefile)->first();

        $state->state_code_lower = $this->formatCountyText($state->state_code, 'lower');
        $state->state_code_normal = $this->formatCountyText($state->state_code, 'normal');
        $state->state_code_plural = $this->formatCountyText($state->state_code, 'plural');

        Cache::remember($city->statefile . '_state', 72000, function () use ($state) {
            return $state;
        });

        $i=0;
        foreach($providers as $provider){
            $i++;
            $provider->logo = $this->formatLogoURL($provider->logo, $provider->id, $i % 9);
            $provider->formatPhone = $this->formatPhoneNumber($provider->phone);
            $provider->momtrusted_phone = $this->formatPhoneNumber(optional(optional($provider)->detail)->momtrusted_phone);
            $provider->introduction = $this->formatProviderDescription($provider);

            $distance = round($this->distance($provider->lat, $provider->lng, $city->latitude, $city->longitude), 1);
            $distance.= $distance > 1 ? ' miles away' : ' mile away';
            $provider->distance = " | " . $distance;
        }

        $parsedUrl = parse_url(url()->current());
        // Get the path and query
        $page_url = $parsedUrl['path'] . (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');
        $questions = Questions::where(function($query) use ($page_url) {
                                $query->where('facility_id', 0)
                                    ->orWhere('page_url', $page_url);
                            })
                            ->where('approved', '1')
                            ->orderBy('created_at', 'desc')
                            ->get();
        foreach ($questions as $question) {
            $answers = Answers::where('question_id', $question->id)
                                ->where('page_url', $page_url)
                                ->where('approved', '1')
                                ->orderBy('created_at', 'desc')
                                ->get();
            if( !empty($answers) ){
                foreach ($answers as $answer) {
                    $answer->answer_by = $this->badWordService->maskBadWords($answer->answer_by);
                    $answer->answer = $this->badWordService->maskBadWords($answer->answer);
                }
                $question->answers = $answers;
            }
            else{
                $question->answers = [];
            }

            $created = Carbon::createFromFormat('Y-m-d H:i:s', $question->created_at);
            $now = Carbon::now();
            $interval = $now->diff($created);

            $question->passed = $this->formatInterval($interval);
            $question->question_by = $this->badWordService->maskBadWords($question->question_by);
            $question->question = $this->badWordService->maskBadWords($question->question);
        }

        return view('homecare.homecare_city', compact('user', 'providers', 'state', 'city', 'zipcodes', 'page', 'topProviders', 'questions', 'page_url'));
    }

    public function zipcode(Request $request)
    {        
        $user = Auth::user();

        $page = request()->query('page');

        $topProviders = [];

        /** @var Zipcode $zipcode */
        $zipcode = Zipcodes::where('zipcode', request()->route()->parameter('zipcode'))->first();
        
        if (!$zipcode) {
            return redirect('/');
        }

        Cache::remember(request()->route()->parameter('zipcode') . '_zipcode', 72000, function () use ($zipcode) {
            return $zipcode;
        });
        
        /** @var State $state */
        $state = States::where('statefile', $zipcode->statefile)->first();

        Cache::remember($zipcode->statefile . '_state', 72000, function () use ($state) {
            return $state;
        });

        $providers = Facility::where('is_center', 0)
                            ->where('approved', '>=', 1)
                            ->where('state', $zipcode->state)
                            ->where('zip', $zipcode->zipcode)
                            ->orderBy('ranking', 'desc')
                            ->orderBy('name', 'asc')
                            ->paginate(30);

        $filename = $zipcode->state . "_" . $zipcode->zipcode . "_homes" . ($page ? "_page_" . ((int) $page) . '_per_page_30' : '');
        $filename = str_replace([" ","-"],"_",$filename);
        $filename = str_replace(["'","."],"",$filename);

        Cache::remember($filename, 21600, function () use ($providers) {
            return $providers;
        });

        if ($zipcode->provider_ids) {
            $ids = explode(',', $zipcode->provider_ids);
            $topProviders = Facility::whereIn('id', $ids)
                                    ->where('is_center', 0)
                                    ->get();

            $i=0;
            foreach($topProviders as $provider){
                $i++;
                $provider->logo = $this->formatLogoURL($provider->logo, $provider->id, $i % 9);
                $provider->formatPhone = $this->formatPhoneNumber($provider->phone);
                $provider->momtrusted_phone = $this->formatPhoneNumber(optional(optional($provider)->detail)->momtrusted_phone);
                $provider->introduction = $this->formatProviderDescription($provider);

                $distance = round($this->distance($provider->lat, $provider->lng, $zipcode->lat, $zipcode->lng), 1);
                $distance.= $distance > 1 ? ' miles away' : ' mile away';
                $provider->distance = " | " . $distance;
            }
        }

        $i=0;
        foreach($providers as $provider){
            $i++;
            $provider->logo = $this->formatLogoURL($provider->logo, $provider->id, $i % 9);
            $provider->formatPhone = $this->formatPhoneNumber($provider->phone);
            $provider->momtrusted_phone = $this->formatPhoneNumber(optional(optional($provider)->detail)->momtrusted_phone);
            $provider->introduction = $this->formatProviderDescription($provider);

            $distance = round($this->distance($provider->lat, $provider->lng, $zipcode->lat, $zipcode->lng), 1);
            $distance.= $distance > 1 ? ' miles away' : ' mile away';
            $provider->distance = " | " . $distance;
        }

        $parsedUrl = parse_url(url()->current());
        // Get the path and query
        $page_url = $parsedUrl['path'] . (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');
        $questions = Questions::where(function($query) use ($page_url) {
                                $query->where('facility_id', 0)
                                    ->orWhere('page_url', $page_url);
                            })
                            ->where('approved', '1')
                            ->orderBy('created_at', 'desc')
                            ->get();
        foreach ($questions as $question) {
            $answers = Answers::where('question_id', $question->id)
                                ->where('page_url', $page_url)
                                ->where('approved', '1')
                                ->orderBy('created_at', 'desc')
                                ->get();
            if( !empty($answers) ){
                foreach ($answers as $answer) {
                    $answer->answer_by = $this->badWordService->maskBadWords($answer->answer_by);
                    $answer->answer = $this->badWordService->maskBadWords($answer->answer);
                }
                $question->answers = $answers;
            }
            else{
                $question->answers = [];
            }

            $created = Carbon::createFromFormat('Y-m-d H:i:s', $question->created_at);
            $now = Carbon::now();
            $interval = $now->diff($created);

            $question->passed = $this->formatInterval($interval);
            $question->question_by = $this->badWordService->maskBadWords($question->question_by);
            $question->question = $this->badWordService->maskBadWords($question->question);
        }

        return view('homecare.homecare_zipcode', compact('user', 'providers', 'topProviders', 'state', 'zipcode', 'page', 'questions', 'page_url'));
    }

    public function geocode($street, $city, $state, $zip)
    {
        $url = "http://maps.googleapis.com/maps/api/geocode/xml?sensor=false";

        $address = $street . " " . $city . " " . $state . " " . $zip;

        $requestUrl = $url . "&address=" . urlencode($address);
        $xml = simplexml_load_file($requestUrl);

        if (!$xml) {
            return false;
        }

        $status = $xml->status;

        if (strcmp($status, "OK") == 0) {
            $location = $xml->result->geometry->location;

            return $location->lng . "," . $location->lat;
        } else {
            return false;
        }
    }

    public function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace("/[^0-9]/", "", $phoneNumber);

		if(strlen($phoneNumber) == 7) {
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phoneNumber);
        } elseif(strlen($phoneNumber) == 10) {
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phoneNumber);
        } else {
            return $phoneNumber;
        }
	}

    public function formatCountyText($stateCode, $option)
    {
        if($stateCode == 'LA') {
            if ($option == 'plural') {
                return 'Parishes';
            } elseif ($option == 'lower') {
                return 'parish';
            } else {
                return 'Parish';
            }  
        } else {
            if ($option == 'plural') {
                return 'Counties';
            } elseif ($option == 'lower') {
                return 'county';
            } else {
                return 'County';
            }
        }
	}

    public function formatURL($url)
    {
        if(preg_match( "/[http|https]:/i",$url)) {
			return "<a href=\"$url\" rel=\"nofollow\" target=\"blank\">$url</a>";			
		} else if(preg_match( "/[@|\s]/i",$url) == false && preg_match( "/\.[com|org|net|info|us|co|biz]/i",$url)) {
			return "<a href=\"http://$url\" rel=\"nofollow\" target=\"blank\">$url</a>";
		} else {
			return $url;
		}
	}

    public function formatLogoURL($logoUrl, $providerId, $count)
    {
        if(preg_match( "/[http|https]:\/\//i", $logoUrl)) {
            return $logoUrl;
        } else if ($logoUrl <> "") {
            return env('IDRIVE_BITBUCKET_URL') . "/" . $logoUrl;
        } else {
            return  "/images/thumb" . $count . ".jpg";
        }
	}

    public function generateFilename($school_name, $school_city, $school_state)
    {
        $name = str_replace(["  "," ","-","(",")","/","@","+","&"],"_",$school_name);
        $name = str_replace([":",".","'",",","#","\""],"",$name);

        $city = str_replace(["  "," ","-","(",")","/","@","+","&"],"_",$school_city);
        $city = str_replace([":",".","'",",","#","\""],"",$city);

        $filename = strtolower($name . "_" . $city . "_" . $school_state);
        $filename = str_replace(["'","&"],"",$filename);
        $filename = str_replace(["___","__","-","(",")","/","@","+"],"_",$filename);
        $filename = substr($filename, 0, 100);

        return $filename;
    }

    public function formatProviderDescription($provider)
    {
        $introduction = preg_replace('/[\s]+/', ' ', $provider->introduction);
        
        if (strlen($introduction) > 40) {
            return $introduction;
        }
        
        if (strlen($provider->type) >= 10) {
            $introduction = $provider->name . ' is a ' . $provider->type . ' in ' . $provider->city . ' ' . $provider->state;
        } else {
            if ($provider->is_center) {
                $introduction = $provider->name . ' is a child care center in ' . $provider->city . ' ' . $provider->state;
            } else {
                $introduction = $provider->name . ' is a home-based daycare in ' . $provider->city . ' ' . $provider->state;
            }
        }

        if ($provider->capacity > 0) {
            $introduction .= ', with a maximum capacity of ' . $provider->capacity . ' children.';
        } else {
            $introduction .= '.';
        }

        if ($provider->age_range <> '') {
            if ($provider->is_center) {
                $introduction .= '  This child care center';
            } else {
                $introduction .= '  The home-based daycare service';
            }
            $introduction .= ' helps with children in the age range of ' . $provider->age_range . '.';
        }
        if ($provider->subsidized == 1) {
            $introduction .= ' The provider also participates in a subsidized child care program.';
        } else {
            $introduction .= ' The provider does not participate in a subsidized child care program.';
        }

        return $introduction;
	}

    public function getZipcodesNearby($latitude, $longitude, $distance = 10, $params = [], $limit = null)
    {
        $query = Zipcodes::select('zipcodes.*')
                        ->selectRaw('((ACOS(SIN(RADIANS('.$latitude.')) * SIN(RADIANS(zipcodes.lat)) + COS(RADIANS('.$latitude.')) * COS(RADIANS(zipcodes.lat)) * COS(RADIANS(('.$longitude.' - zipcodes.lng)))) * 180 / PI()) * 60 * 1.1515) AS distance')
                        ->where('zipcodes.lat', '<', round($latitude, 1) + 0.4)
                        ->where('zipcodes.lat', '>', round($latitude, 1) - 0.4)
                        ->having('distance', '<=', $distance)
                        ->orderBy('distance', 'asc');

        foreach ($params as $field => $value) {
            switch ($field) {
                case 'minCenterCount':
                    $query->where('zipcodes.center_count', '>=', $value);
                    break;
                case 'minHomebaseCount':
                    $query->where('zipcodes.homebase_count', '>=', $value);
                    break;
                default:
                    if (is_array($value)) {
                        $query->whereIn('zipcodes.' . $field, $value);
                    } elseif (is_string($value)) {
                        $query->where('zipcodes.' . $field, $value);
                    } else {
                        $query->where('zipcodes.' . $field, '=', $value);
                    }
            }
        }

        if ($limit) {
            $query->limit($limit);
        }

        $zipcodes = $query->get();

        return $zipcodes;
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit = 'm')
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } elseif ($unit == "N") {
            return ($miles * 0.8684);
        }

        return $miles;
    }

    public function formatInterval($interval) {
        if ($interval->y > 0) {
            return $interval->y . ' year' . ($interval->y > 1 ? 's' : '');
        } elseif ($interval->m > 0) {
            return $interval->m . ' month' . ($interval->m > 1 ? 's' : '');
        } elseif ($interval->d > 0) {
            return $interval->d . ' day' . ($interval->d > 1 ? 's' : '');
        } elseif ($interval->h > 0) {
            return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '');
        } elseif ($interval->i > 0) {
            return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '');
        } else {
            return $interval->s . ' second' . ($interval->s > 1 ? 's' : '');
        }
    }
}
