<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use ReCaptcha\ReCaptcha;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use App\Models\Facility;
use App\Models\Facilitylog;
use App\Models\Facilityhours;
use App\Models\Facilitydetail;
use App\Models\Reviews;
use App\Models\Images;
use App\Models\Inspections;
use App\Models\News;
use App\Models\States;
use App\Models\Cities;
use App\Models\Counties;
use App\Models\Mtleads;
use App\Models\Questions;
use App\Models\Answers;
use Carbon\Carbon;
use App\Services\BadWordService;

class ProviderController extends Controller
{
    protected $badWordService;

    public function __construct(BadWordService $badWordService)
    {
        $this->badWordService = $badWordService;
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->is_provider) {
            return view('provider.index_notsignin', compact('user'));
        }

        if (request()->query('pid')) {
            /** @var Facility $provider */
            $provider = Facility::where('id', request()->query('pid'))->first();
            $provider->formatPhone = $this->formatPhoneNumber($provider->phone);

            return view('provider.index_hasprovider', compact('user', 'provider'));
        }
        elseif ($user->multi_listings) {
            $providers = Facility::where('user_id', $user->id)
                                ->orderBy('name', 'asc')
                                ->paginate(50);
            
            if (count($providers)) {
                return view('provider.index_hasmultiproviders', compact('user', 'providers'));
            }
            else {
                return redirect('/provider/find');
            }
        }
        elseif ($user->provider) {
            $provider = $user->provider;
            $provider->formatPhone = $this->formatPhoneNumber($provider->phone);

            return view('provider.index_hasprovider', compact('user', 'provider'));
        }
        
        $providers = Facility::where('state', $user->state)
                            ->where('zip', $user->zip)
                            ->where('is_center', $user->caretype == 'CENTER')
                            ->where('user_id', null)
                            ->orderBy('name', 'asc')
                            ->get();
        
        return view('provider.index', compact('user', 'providers'));
    }

    public function find(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->is_provider) {
            return view('provider.index_notsignin', compact('user'));
        }

        $method = $request->method();

        $message = "";

        if ($method == "POST") {
            if (!$request->name || (!$request->zip && !$request->city)) {
                $message = 'You must enter Childcare Name and either ZIP code or City to search';

                return view('provider.find', compact('user', 'message', 'request'));
            }

            $query = Facility::where('is_center', $user->caretype == 'CENTER');

            if($request->name){
                $query->where('name', 'like', '%' . $request->name . '%');
            }

            if($request->zip){
                $query->where('zip', $request->zip);
            }

            if($request->city){
                $query->where('city', $request->city);
            }

            $providers = $query->orderBy('name', 'asc')->paginate(51);

            $i=0;
            foreach($providers as $provider){
                $i++;
                $provider->formatPhone = $this->formatPhoneNumber($provider->phone);
            }

            if (!count($providers)) {
                $message = 'There is no result for your selected criteria.  Please try again with different criteria.';
            } elseif (count($providers) > 50) {
                $message = 'There are too many listings return.  Please narrow your search criteria.';
            }

            $allowAdd = 1;

            return view('provider.find', compact('user', 'providers', 'message', 'allowAdd', 'request'));
        }

        return view('provider.find', compact('user'));
    }

    public function new(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/provider');
        }

        $method = $request->method();

        if ($method == "POST") {
            $valid_item = [
                'name' => 'required|min:5',
                'address' => 'required|min:5',
                'city' => 'required|min:3',
                'zip' => 'required|min:5',
                'phone' => 'required|min:10',
                'introduction' => 'required|min:10',
                'capacity' => 'required',
                'typeofcare' => 'required|array',
            ];

            if($request->language){
                $valid_item['language'] = 'required|min:4';
            }

            $validated = $request->validate($valid_item);
            
            $provider = Facility::where('name', trim($request->name))
                                ->where('zip', $request->zip)
                                ->first();

            if (!$provider) {
                $typeofcare = join(', ', $request->typeofcare);
                if ($request->transportation && is_array($request->transportation)) {
                    $transportation = join(', ', $request->transportation);
                }
                else{
                    $transportation = '';
                }

                $name = str_replace(["`",":",".","'",",","#","\"","\\"],"", $request->name);
                $name = preg_replace('/[^a-zA-Z\d-]/', '-', $name);

                /** @var string $city */
                $city = str_replace([":",".","'",",","#","\""],"", $request->city);
                $city = preg_replace('/[^a-zA-Z\d]/', '-', $city);

                $filename = strtolower($name . "-" . $city . "-" . $user->state);
                $filename = str_replace(["'","&","."],"",$filename);
                $filename = preg_replace('/[^a-zA-Z\d-]/', '-', $filename);
                $filename = preg_replace('/-{2,}/', '-', $filename);

                $push_data = [
                    'name' => $request->name,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => '',
                    'zip' => $request->zip,
                    'phone' => $request->phone,
                    'website' => ($request->website) ? $request->website : "",
                    'operation_id' => ($request->operationId) ? $request->operationId : "",
                    'introduction' => $request->introduction,
                    'capacity' => $request->capacity,
                    'age_range' => $request->ageRange,
                    'pricing' => $request->pricing,
                    'typeofcare' => $typeofcare,
                    'schools_served' => $request->schoolsServed,
                    'language' => $request->language,
                    'accreditation' => $request->accreditation,
                    'subsidized' => $request->subsidized,
                    'transportation' => $transportation,
                    'additionalInfo' => $request->additionalinfo,
                    'created_date' => new \DateTime(),
                    'type' => '',
                    'is_center' => 0,
                    'filename' => $filename,
                    'approved' => 0,
                ];
    
                $provider = Facility::create($push_data);

                $provider->is_center = ($user->caretype == 'CENTER');
                if($user->caretype == 'CENTER'){
                    $provider->type = 'Child Care Center';
                }
                elseif($user->caretype == 'HOME'){
                    $provider->type = 'Home Daycare';
                }
                $provider->contact_firstname = $user->firstname;
                $provider->contact_lastname = $user->lastname;
                $provider->email = $user->email;
                $provider->state = $user->state;
                $provider->user_id = $user->id;

                $city = Cities::where('state', $provider->state)
                                ->where('city', $provider->city)
                                ->first();

                $filename = strtolower($provider->city . "_" . $provider->state . "_city");
                $filename = str_replace([" ","-"],"_",$filename);
                $filename = str_replace(["'","."],"",$filename);

                Cache::remember($filename, 72000, function () use ($city) {
                    return $city;
                });

                if ($city) {
                    $provider->cityfile = $city->filename;
                    $provider->county = $city->county;
                }

                $provider->save();
            }

            $user->provider_id = $provider->id;
            $user->save();

            return redirect('/provider');
        }

        $message = 'Please enter your information and click Submit.';
        
        return view('provider.new', compact('user', 'message', 'request'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $providerId = $request->pid ?: $request->pid;

        if (!$user || !$user->provider && !$providerId) {
            return redirect('/provider');
        }

        if ($providerId) {
            /** @var Facility $provider */
            $provider = Facility::where('id', $providerId)->first();
        } else {
            /** @var Facility $provider */
            $provider = $user->provider;
        }

        if (!$provider) {
            return redirect('/provider');
        }

        $method = $request->method();

        if ($method == "POST") {
            $valid_item = [
                'address' => 'required|min:5',
                'phone' => 'required|min:10',
                'capacity' => 'required',
                'typeofcare' => 'required|array',
            ];

            if($request->introduction){
                $valid_item['introduction'] = 'required|min:10';
            }

            if($request->language){
                $valid_item['language'] = 'required|min:4';
            }

            $validated = $request->validate($valid_item);

            $typeofcare = join(', ', $request->typeofcare);
            if ($request->transportation && is_array($request->transportation)) {
                $transportation = join(', ', $request->transportation);
            }
            else{
                $transportation = '';
            }

            if ($provider->approved == 0) {
                $provider->address = $request->address;
                $provider->phone = $request->phone;
                $provider->email = ($request->email) ? $request->email : "";
                $provider->website = ($request->website) ? $request->website : "";
                $provider->operation_id = ($request->operationId) ? $request->operationId : "";
                $provider->introduction = ($request->introduction) ? $request->introduction : "";
                $provider->capacity = $request->capacity;
                $provider->age_range = ($request->ageRange) ? $request->ageRange : "";
                $provider->pricing = ($request->pricing) ? $request->pricing : "";
                $provider->typeofcare = $typeofcare;
                $provider->schools_served = ($request->schoolsServed) ? $request->schoolsServed : "";
                $provider->language = ($request->language) ? $request->language : "";
                $provider->accreditation = ($request->accreditation) ? $request->accreditation : "";
                $provider->subsidized = ($request->subsidized) ? $request->subsidized : 0;
                $provider->transportation = $transportation;
                $provider->additionalInfo = ($request->additionalinfo) ? $request->additionalinfo : "";
    
                $provider->save();

                $success = 1;
                $provider->formatPhone = $this->formatPhoneNumber($provider->phone);

                return view('provider.update', compact('user', 'request', 'provider', 'success'));
            } else {
                $providerLog = Facilitylog::where('approved', 0)
                                        ->where('provider_id', $provider->id)
                                        ->first();
                
                if (is_null($providerLog)) {
                    // create new facilityLog
                    $push_data = [
                        'provider_id' => $providerId,
                        'name' => $provider->name,
                        'email' => ($request->email) ? $request->email : "",
                        'address' => $request->address,
                        'address2' => '',
                        'city' => $provider->city,
                        'state' => $provider->state,
                        'zip' => $provider->zip,
                        'phone' => $request->phone,
                        'website' => ($request->website) ? $request->website : "",
                        'operation_id' => ($request->operationId) ? $request->operationId : "",
                        'introduction' => ($request->introduction) ? $request->introduction : "",
                        'capacity' => $request->capacity,
                        'age_range' => ($request->ageRange) ? $request->ageRange : "",
                        'pricing' => ($request->pricing) ? $request->pricing : "",
                        'typeofcare' => $typeofcare,
                        'schools_served' => ($request->schoolsServed) ? $request->schoolsServed : "",
                        'language' => ($request->language) ? $request->language : "",
                        'accreditation' => ($request->accreditation) ? $request->accreditation : "",
                        'subsidized' => ($request->subsidized) ? $request->subsidized : 0,
                        'transportation' => $transportation,
                        'additionalInfo' => ($request->additionalinfo) ? $request->additionalinfo : "",
                        'daysopen' => '',
                        'hoursopen' => '',
                        'approved' => 0,
                        'user_id' => $user->id,
                    ];
        
                    $providerLog = Facilitylog::create($push_data);
                }
                
                // $providerLog->user_id = $user->id;
                // $providerLog->name = $provider->name;

                $success = 1;
                
                return view('provider.update', compact('user', 'request', 'providerLog', 'provider', 'success'));
            }
        }
        else{
            if (empty($provider->email)) {
                $provider->email = $user->email;
            }
            
            if (!$provider->approved == 0) {                
                $providerLog = Facilitylog::where('approved', 0)
                                        ->where('provider_id', $provider->id)
                                        ->first();
                
                if (!is_null($providerLog)) {
                    $providerLog->formatPhone = $this->formatPhoneNumber($providerLog->phone);
                    return view('provider.update', compact('user', 'request', 'provider', 'providerLog'));
                }
            }

            $provider->formatPhone = $this->formatPhoneNumber($provider->phone);
            
            return view('provider.update', compact('user', 'request', 'provider'));
        }
    }

    public function updateOperationHours(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/provider');
        }

        $providerId = $request->pid;

        if ($providerId) {
            /** @var Facility $provider */
            $provider = Facility::where('id', $providerId)->first();
        } else {
            /** @var Facility $provider */
            $provider = $user->provider;
        }

        if (!$provider) {
            return redirect('/provider');
        }
        
        $message = '';
        $method = $request->method();

        if($provider->operationHours){
            $operationHours = $provider->operationHours;
        }
        else{
            $operationHours = Facilityhours::create([
                'facility_id' => $providerId
            ]);
        }

        if ($method == "POST") {
            if(!$request->monday && !$request->tuesday && !$request->wednesday && !$request->thursday && !$request->friday && !$request->saturday && !$request->sunday){
                $message = 'You must fill out at least one weekday.';
            }
            else{
                $operationHours->monday = $request->monday;
                $operationHours->tuesday = $request->tuesday;
                $operationHours->wednesday = $request->wednesday;
                $operationHours->thursday = $request->thursday;
                $operationHours->friday = $request->friday;
                $operationHours->saturday = $request->saturday;
                $operationHours->sunday = $request->sunday;

                $provider->operationHours = $operationHours;

                $operationHours->save();

                $message = 'Operation hours have been updated successfully.';
            }
        }

        return view('provider.update_operation_hours', compact('user', 'provider', 'message', 'request'));
    }

    public function claim(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/provider');
        }

        if (!$request->isMethod('post') || !$user->is_provider) {
            return response()->json(['error' => 'Not Found'], 404);
        }
        
        /** @var Facility $provider */
        $provider = Facility::find($request->id);

        if ($provider->user_id) {
            return response()->json(['error' => sprintf('User is already specified for the provider with id %s', $facility->id)], 400);
        }

        $user->provider_id = $provider->id;
        $user->save();

        return redirect('/provider');
    }

    public function mtsave(Request $request)
    {
        if (!$request->ajax() || !$request->isMethod('post')) {
            return response()->json(['error' => 'Not Found'], 404);
        }
        
        $mtlead = new Mtleads();
        $mtlead->provider_id = $request->pid;
        $mtlead->name = $request->n;
        $mtlead->email = $request->e;
        $mtlead->phone = $request->p;
        $mtlead->childage = $request->ca;
        $mtlead->requested_date = $request->rd;
        $mtlead->ip_address = $_SERVER['REMOTE_ADDR'];

        if ($_SERVER['HTTP_REFERER'] <> '') {
            $mtlead->server_uri = $_SERVER['HTTP_REFERER'];
        } else {
            $mtlead->server_uri = $_SERVER['REQUEST_URI'];
        }

        $mtlead->user_agent = $_SERVER['HTTP_USER_AGENT'];

        $mtlead->save();

        return response()->json([]);
    }

    public function view(Request $request)
    {
        $user = Auth::user();

        $filename = request()->route()->parameter('filename');

        if (!$filename && !$request->id) {
            return redirect('/');
        }

        /* if ($this->iptracker->checkAndUpdate()) {
            return $this->redirect()->toUrl('/verify?backUrl=' . $_SERVER['REQUEST_URI']);
        } */

        if ($filename) {
            /** @var Facility $provider */
            $provider = Facility::where('filename', $filename)
                                ->whereIn('approved', ['0', '1', '2', '-1', '-2','-4'])
                                ->first();
        } else {
            /** @var Facility $provider */
            $provider = Facility::where('id', $request->id)->first();
        }
        
        if (!$provider) {
            return redirect('/');
        }
        if ($provider->approved == "-4") {
            if ($provider->parent_handbook_url) {
                return redirect('/provider_detail/'.$provider->parent_handbook_url);
            } else {
                return redirect('/');
            }
        }

        if (!$provider->lat || !$provider->lng) {
            $coordinates = $this->geocode($provider->address, $provider->city, $provider->state, $provider->zip);

            if ($coordinates) {
                list($longitude, $latitude) = explode(",", $coordinates,2);
                $provider->lat = $latitude;
                $provider->lng = $longitude;
            }
        }

        if (!$provider->detail) {
            $facilityDetail = FacilityDetail::create([
                'facility_id' => $provider->id
            ]);
            // $provider->detail = $facilityDetail;
        }

        if (!optional(optional($provider)->detail)->gmap_pano_id) {
            $params = [
                'location' => $provider->lat . ',' . $provider->lng,
                'key' => env('GOOGLE_API_KEY')
            ];

            $ch = curl_init('https://maps.googleapis.com/maps/api/streetview/metadata?' . http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);

            $response = curl_exec($ch);

            curl_close($ch);

            $result = json_decode($response);

            if ($result->status == 'OK') {
                $provider->detail->gmap_pano_id = $result->pano_id;

                $provider->detail->save();
            }
        }

        // $this->layout()->setVariables([
        //     'lng' => $provider->getLng(),
        //     'lat' => $provider->getLat()
        // ]);

        $provider->visits = $provider->visits + 1;
        
        $provider->save();
        
        $reviews = Reviews::where('facility_id', $provider->id)
                            ->where('approved', 1)
                            ->orderBy('review_date', 'desc')
                            ->get();

        $images = Images::where('provider_id', $provider->id)
                        ->where('type', 'CENTER')
                        ->where('approved', 1)
                        ->get();

        $inspections = Inspections::where('facility_id', $provider->id)
                        ->orderBy('report_date', 'desc')
                        ->orderBy('report_type', 'asc')
                        ->limit(15)
                        ->get();

        Cache::remember($provider->id . '_inspections', 72000, function () use ($inspections) {
            return $inspections;
        });

        $news = News::where('provider_id', $provider->id)
                        ->orderBy('created_date', 'desc')
                        ->get();

        $paged = ($request->page) ? $request->page : 1;
        if ($provider->is_center) {
            $providers = Facility::where('is_center', 1)
                                ->where('approved', '>=', 1)
                                ->where('state', $provider->state)
                                ->where('zip', $provider->zip)
                                ->orderBy('ranking', 'desc')
                                ->orderBy('name', 'asc')
                                ->paginate(75);

            $filename = $provider->state . "_" . $provider->zip . "_centers" . ($paged ? "_page_" . ((int) $paged) . '_per_page_75' : '');
            $filename = str_replace([" ","-"],"_",$filename);
            $filename = str_replace(["'","."],"",$filename);
    
            Cache::remember($filename, 21600, function () use ($providers) {
                return $providers;
            });
        } else {
            $providers = Facility::where('is_center', 0)
                                ->where('approved', '>=', 1)
                                ->where('state', $provider->state)
                                ->where('zip', $provider->zip)
                                ->orderBy('ranking', 'desc')
                                ->orderBy('name', 'asc')
                                ->paginate(75);

            $filename = $provider->state . "_" . $provider->zip . "_homes" . ($paged ? "_page_" . ((int) $paged) . '_per_page_75' : '');
            $filename = str_replace([" ","-"],"_",$filename);
            $filename = str_replace(["'","."],"",$filename);
    
            Cache::remember($filename, 21600, function () use ($providers) {
                return $providers;
            });
        }

        /** @var State $state */
        $state = States::where('state_code', $provider->state)->first();

        Cache::remember($provider->state . '_state', 72000, function () use ($state) {
            return $state;
        });

        $nearestProviders = [];
        if (count($providers) < 6 && $state) {
            if ($provider->is_center) {
                $nearestProviders = $this->getCentersNearbyCenter($provider->id, $state->state_code, $provider->county, (float)$provider->lat, (float)$provider->lng, 30, 20);
            } else {
                $nearestProviders = $this->getHomeDaycaresNearbyHomeDaycare($provider->id, $state->state_code, $provider->county, (float)$provider->lat, (float)$provider->lng, 30, 20);
            }

            foreach($nearestProviders as $nearestProvider){
                $nearestProvider->formatPhone = $this->formatPhoneNumber($nearestProvider->phone);
                $distance = round($this->distance($nearestProvider->lat, $nearestProvider->lng, $provider->lat, $provider->lng), 1);
                $distance.= $distance > 1 ? ' miles away' : ' mile away';
                $nearestProvider->distance = " | " . $distance;
            }
        }

        $reviewsVoted = $request->cookie('reviews_voted') ? unserialize($request->cookie('reviews_voted')) : [];
        
        $county = [];
        if ($provider->county) {
            $county = Counties::where('state', $provider->state)
                            ->where('county', $provider->county)
                            ->first();
            
            $filename = strtolower($provider->county . "_" . $provider->state . "_county");
            $filename = str_replace([" ","-"],"_",$filename);
            $filename = str_replace(["'","."],"",$filename);
    
            Cache::remember($filename, 72000, function () use ($county) {
                return $county;
            });
        }

        // if ($provider->is_featured || $request->page == 'feature') {
        //     $this->layout()->setVariable('mapzoom', 14);
        // }

        $provider->formatPhone = $this->formatPhoneNumber($provider->phone);
        $provider->momtrusted_phone = $this->formatPhoneNumber(optional(optional($provider)->detail)->momtrusted_phone);
        $provider->maskPhone = $this->maskPhoneNumber($provider->phone);
        $provider->logo = $this->formatLogoURL($provider->logo, $provider->id, rand(0, 8));
        $provider->introduction = $this->formatProviderDescription($provider);
        $provider->website = $this->formatWebsite($provider->website, 'Company Website');
        $provider->state_rating_text = $this->formatStateRatingText($provider->state);

        foreach($reviews as $review){
            $review->review_by = $this->badWordService->maskBadWords($review->review_by);
            $review->comments = $this->badWordService->maskBadWords($review->comments);
            $totalVotes = $review->helpful + $review->nohelp;
            if (!$totalVotes) {
                $review->helpful_text = '';
            } else {
                $review->helpful_text = sprintf('%s out of %s think this review is helpful', $review->helpful, $totalVotes);
            }
        }

        $providerId = $provider->id;

        $questions = Questions::where(function($query) use ($providerId) {
                                $query->where('facility_id', 0)
                                    ->orWhere('facility_id', $providerId);
                            })
                            ->where('approved', '1')
                            ->orderBy('created_at', 'desc')
                            ->get();
        foreach ($questions as $question) {
            $answers = Answers::where('question_id', $question->id)
                            ->where('facility_id', $providerId)
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
        
		if (request()->route()->parameter('format') == 'amp') {
	        return view('provider.amp_new_view', compact('user', 'provider', 'providers', 'reviews', 'reviewsVoted', 'images', 'inspections', 'news', 'state', 'county', 'nearestProviders', 'questions'));
		}
        
        return view('provider.view', compact('user', 'provider', 'providers', 'reviews', 'reviewsVoted', 'images', 'inspections', 'news', 'state', 'county', 'nearestProviders', 'questions'));
    }

    public function maskBadWords($text)
    {
        $badWords = [
            'fuck', 'fucks', 'shit', 'nigger', 'cock', 'porn', 'ass', 'whore', 'bitch', 'whores','Ilana','cocks','tits','teets',''
        ];

        $filteredText = preg_replace_callback(
            $this->createSearchPattern($badWords),
            function ($matches) {
                // Check if matches exist and are not empty
                if (!empty($matches[0])) {
                    return $matches[0][0] . str_repeat('*', strlen($matches[0]) - 1);
                }
                return $matches[0]; // Fallback if no matches
            }, $text
        );
        
        return $filteredText !== null ? $filteredText : $text;
    }

    protected function createSearchPattern($badWords)
    {
        return array_map(function ($word) {
            return '/\b' . preg_quote($word, '/') . '\b/i'; // Use preg_quote to escape special characters
        }, $badWords);
    }

    public function formatStateRatingText($stateCode)
    {
        if ($stateCode == 'DC') {
            return 'Capital Quality Designation';
        } elseif ($stateCode == 'GA') {
            return 'Quality Rated Star';
        } elseif ($stateCode == 'IA') {
            return 'Quality Rating System (QSR) Level';
        } elseif ($stateCode == 'IN') {
            return 'PTQ Level';
        } elseif ($stateCode == 'KY') {
            return 'All STARS Rating';
        } elseif ($stateCode == 'NM') {
            return 'Quality Rating';
        } elseif ($stateCode == 'OH') {
            return 'Step Up To Quality Rating';
        } elseif($stateCode == 'SC') {
            return 'ABC Quality Level';
        } elseif ($stateCode == 'VA') {
            return 'Virginia Quality Level';
        } elseif ($stateCode == 'WA') {
            return 'Early Achievers Rating';
        } elseif ($stateCode == 'WI') {
            return 'YoungStar Rating';
        } else {
            return 'State Rating';
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

    public function maskPhoneNumber($phoneNumber)
    {
        $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $phoneNumber);

		if (strlen($strippedPhoneNumber) == 7) {
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-xxxx", $strippedPhoneNumber);
        } elseif(strlen($strippedPhoneNumber) == 10) {
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-xxxx", $strippedPhoneNumber);
        }

		return $phoneNumber;
	}

    public function formatLogoURL($logoUrl, $providerId, $count)
    {
        if(preg_match( "/[http|https]:\/\//i", $logoUrl)) {
            return $logoUrl;
        } else if ($logoUrl <> "") {
            return env('IDRIVE_BITBUCKET_URL') . "/" . $logoUrl;
        } else {
            return "/images/thumb" . $count . ".jpg";
        }
	}

    public function formatWebsite($url, $name)
    {
        if(preg_match( "/[http|https]:/i", $url)) {
			return "<a href=\"$url\" rel=\"nofollow\" target=\"_blank\">$name</a>";
		} else if(preg_match( "/[@|\s]/i",$url) == false && preg_match( "/\.[com|org|net|info|us|co|biz]/i", $url)) {
		    return "<a href=\"http://$url\" rel=\"nofollow\" target=\"_blank\">$name</a>";
		}

        return $url;
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

    public function getCentersNearbyCenter($providerId, $stateCode, $county, $latitude, $longitude, $distance = 10, $limit = null)
    {
        $query = Facility::select('facility.*')
                        ->selectRaw('((ACOS(SIN(RADIANS('.$latitude.')) * SIN(RADIANS(facility.lat)) + COS(RADIANS('.$latitude.')) * COS(RADIANS(facility.lat)) * COS(RADIANS(('.$longitude.' - facility.lng)))) * 180 / PI()) * 60 * 1.1515) AS distance')
                        ->where('facility.is_center', 1)
                        ->where('facility.approved', '>=', 1)
                        ->where('facility.state', $stateCode)
                        ->where('facility.county', $county)
                        ->where('facility.id', '<>', $providerId)
                        ->where('facility.lat', '<', round($latitude, 1) + 0.4)
                        ->where('facility.lat', '>', round($latitude, 1) - 0.4)
                        ->having('distance', '<=', $distance)
                        ->orderBy('distance', 'asc');

        if ($limit) {
            $query->limit($limit);
        }

        $providers = $query->get();

        return $providers;
    }

    public function getHomeDaycaresNearbyHomeDaycare($providerId, $stateCode, $county, $latitude, $longitude, $distance = 10, $limit = null)
    {
        $query = Facility::select('facility.*')
                        ->selectRaw('((ACOS(SIN(RADIANS('.$latitude.')) * SIN(RADIANS(facility.lat)) + COS(RADIANS('.$latitude.')) * COS(RADIANS(facility.lat)) * COS(RADIANS(('.$longitude.' - facility.lng)))) * 180 / PI()) * 60 * 1.1515) AS distance')
                        ->where('facility.is_center', 0)
                        ->where('facility.approved', '>=', 1)
                        ->where('facility.state', $stateCode)
                        ->where('facility.county', $county)
                        ->where('facility.id', '<>', $providerId)
                        ->where('facility.lat', '<', round($latitude, 1) + 0.4)
                        ->where('facility.lat', '>', round($latitude, 1) - 0.4)
                        ->having('distance', '<=', $distance)
                        ->orderBy('distance', 'asc');

        if ($limit) {
            $query->limit($limit);
        }

        $providers = $query->get();

        return $providers;
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
