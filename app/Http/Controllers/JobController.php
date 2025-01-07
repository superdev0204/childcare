<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use Carbon\Carbon;
use ReCaptcha\ReCaptcha;
use App\Models\Jobs;
use App\Models\States;
use App\Models\Cities;
use App\Models\Careerbuilderlog;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $jobs = Jobs::where('approved', 1)
                    ->where(function($query) {
                        $query->where('endDate', '>=', DB::raw('created'))
                            ->orWhereNull('endDate');
                    })
                    ->where('created', '>=', DB::raw("DATE_SUB(CURRENT_DATE(), INTERVAL 10 DAY)"))
                    ->orderBy('created', 'desc')
                    ->limit(10)
                    ->get();
        
        $states = States::where('country', 'US')->orderBy('state_name', 'asc')->get();

        return view('job.job', compact('user', 'states', 'jobs'));
    }
    
    public function state(Request $request)
    {
        $user = Auth::user();

        $statefile = request()->route()->parameter('state');
        
        $state = States::where('statefile', $statefile)->first();

        if (!$state) {
            return redirect('/');
        }

        Cache::remember($statefile . '_state', 21600, function () use ($state) {
            return $state;
        });

        $cities = [];

        // $state->state_code_lower = $this->formatCountyText($state->state_code, 'lower');
        // $state->state_code_normal = $this->formatCountyText($state->state_code, 'normal');
        // $state->state_code_plural = $this->formatCountyText($state->state_code, 'plural');        

        if ($state->jobs_count < 10) {
            $jobs = Jobs::where('approved', 1)
                        ->where(function($query) {
                            $query->where('endDate', '>=', DB::raw('created'))
                                ->orWhereNull('endDate');
                        })
                        ->where('state', $state->state_code)
                        ->get();
        } else {
            $jobs = Jobs::where('approved', 1)
                        ->where(function($query) {
                            $query->where('endDate', '>=', DB::raw('created'))
                                ->orWhereNull('endDate');
                        })
                        ->where('state', $state->state_code)
                        ->orderBy('created', 'desc')
                        ->limit(5)
                        ->get();

            $cities = Cities::where('state', $state->state_code)
                            ->where('jobs_count', '>', 0)
                            ->orderBy('city', 'asc')
                            ->get();
        }
        
        return view('job.job_state', compact('user', 'jobs', 'state', 'cities'));
    }

    public function city(Request $request)
    {        
        $user = Auth::user();

        $cityname = request()->route()->parameter('city');

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

        $state = States::where('statefile', $city->statefile)->first();

        Cache::remember($city->statefile . '_state', 72000, function () use ($state) {
            return $state;
        });

        if ($city->jobs_count > 0) {
            $jobs = Jobs::where('approved', 1)
                        ->where(function($query) {
                            $query->where('endDate', '>=', DB::raw('CURRENT_DATE()'))
                                ->orWhereNull('endDate');
                        })
                        ->where('state', $state->state_code)
                        ->where('city', $city->city)
                        ->get();
        } else {
            $jobs = Jobs::where('approved', 1)
                        ->where('endDate', '>=', DB::raw("DATE_SUB(CURRENT_DATE(), INTERVAL 3 MONTH)"))
                        ->where('state', $state->state_code)
                        ->where('city', $city->city)
                        ->get();
        }

        $centers = [];
        if (empty($jobs)) {
            $centers = Facility::where('is_center', 1)
                                ->where('approved', 1)
                                ->where('state', $state->state_code)
                                ->where('city', $city->city)
                                ->where('website', '!=', '')
                                ->limit(20)
                                ->get();
        }

        return view('job.job_city', compact('user', 'jobs', 'state', 'city', 'centers'));
    }

    public function view(Request $request)
    {
        $user = Auth::user();
        
        /** @var Jobs $job */
        $job = Jobs::where('id', $request->id)->first();
        
        if (!$job) {
            return redirect('/');
        }
        
        if ($job->source == 'CAREERBUILDER' && !$job->detail) {
            $careerBuilderLog = new Careerbuilderlog();
            $careerBuilderLog->type = 'job';
            $careerBuilderLog->key = 'WDha5X573H5XBN4XWY6Q';
            $careerBuilderLog->source = 'JobsController';
            $careerBuilderLog->request = $job->jobServiceURL;

            $careerBuilderLog->save();

            $ch = curl_init($job->jobServiceURL);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);

            curl_close($ch);
            // Get the XML from the response, bypassing the header
            if ($xml = strstr($response, '<?xml')) {
                $xml = simplexml_load_string($xml);

                if ($xml->Errors->Error != "") {
                    $job->description = "This job has been filled or not available anymore.  Please apply for another job instead.";
                    $job->approved = -1;
                    $job->hasDetail = false;
                } else {
                    $job->description = html_entity_decode($xml->Job->JobDescription);
                    $job->requirements = html_entity_decode($xml->Job->JobRequirements);
                    $job->education = $xml->Job->DegreeRequired;
                    $job->experienceRequired = $xml->Job->ExperienceRequired;
                    $job->applyURL = $xml->Job->ApplyURL;
                    $job->zip = $xml->Job->LocationPostalCode;
                    $job->endDate = \DateTime::createFromFormat('m/d/Y', $xml->Job->EndDate);
                    $job->startDate = \DateTime::createFromFormat('m/d/Y', $xml->Job->BeginDate);

                    $job->hasDetail = true;
                }

                $job->save();
            }
        }

        /** @var State $state */
        $state = States::where('state_code', $job->state)->first();

        return view('job.job_view', compact('user', 'job', 'state'));
    }

    public function verify(Request $request)
    {
        $user = Auth::user();

        if (!$request->id || !$request->e) {
            return redirect('/');
        }

        /** @var Jobs $job */
        $job = Jobs::where('id', $request->id)
                    ->where('email', $request->e)
                    ->first();

        if (!$job) {
            return redirect('/');
        }

        $job->email_verified = true;
        $job->save();

        $message = 'Thank you for verify your email address.  Your job posting will be reviewed for approval within 1-2 business days.';

        return view('job.job_verify', compact('user', 'job', 'message'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        $states = States::all();
        
        $method = $request->method();
        $recaptcha = new ReCaptcha(env('DATA_SECRETKEY'));
        $response = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());
        $educations = array(
            "High School" => "High School",
            "2 Year Degree" => "2 Year Degree",
            "College Degree" => "College Degree",
            "Graduate Level" => "Graduate Level",
        );
        $message = "Please make the following corrections and submit again.";
        $success = 0;

        if ($method == "POST") {
            $valid_item = [
                'title' => 'required|min:5',
                'description' => 'required',
                'company' => 'required|min:5',
                'phone' => 'required|min:10',
                'email' => 'required',
                'city' => 'required|min:3',
                'state' => 'required',
                'zip' => 'required|min:5',
                'education' => 'required',
                'requirements' => 'required',
                'howtoapply' => 'required',
            ];

            if ($response->isSuccess()) {
                // reCAPTCHA verification successful, process the form submission
            }
            else{
                $valid_item['recaptcha-token'] = 'required';
            }

            $validated = $request->validate($valid_item);
            
            $push_data = [
                'title' => $request->title,
                'description' => $request->description,
                'state' => $request->state,
                'city' => $request->city,
                'zip' => $request->zip,
                'education' => $request->education,
                'rate_range' => ($request->rate_range) ? $request->rate_range : "",
                'requirements' => $request->requirements,
                'phone' => $request->phone,
                'email' => $request->email,
                'created' => new \DateTime(),
                'approved' => 0,
                'email_verified' => false,
                'company' => $request->company,
                'source' => "",
                'ext_id' => "",
                'employmentType' => "",
                'jobServiceURL' => "",
                'jobDetailsURL' => "",
                'companyDetailsURL' => "",
                'companyImageURL' => "",
                'applyURL' => "",
                'experienceRequired' => "",
                'hasDetail' => 0,
                'howtoapply' => $request->howtoapply,
            ];

            $job = Jobs::create($push_data);

            if ($user) {
                $job->user_id = $user->id;
                $job->email_verified = true;
                $job->save();
            }

            if ($user) {
                $message = "Thank you for posting your job requirements. <br/><br/>";
                $message .= "Your job posting will be reviewed for approval within 1-2 business days. <br/>";
            } else {
                $message = "Thank you for posting your job requirements. <br/><br/>";

                $mailMessage = "Hello, <br/><br/>";
                $mailMessage .= "Thank you for posting the job requirements for " . $job->title . " on ChildcareCenter.us.<br/><br/> ";
                $mailMessage .= "To verify that you are the person submitted the job " . $job->title . ", please follow this link:<br/><br/>";
                $mailMessage .= $request->getSchemeAndHttpHost() . "/jobs/verifyjob?id=" . $job->id . "&e=" . $job->email . "<br/><br/>";
                $mailMessage .= "If you are unable to click on the link above, copy and paste the full link into your web browser's address bar and press enter. <br/><br/>";
                $mailMessage .= "Thanks sincerely<br/>";
                
                try {
                    $data = array(
                        'from_name' => config('mail.from.name'),
                        'from_email' => config('mail.from.address'),
                        'subject' => 'Job posting for ' . $job->title,
                        'message' => $mailMessage,
                    );

                    Mail::to($job->email)->send(new SendEmail($data));

                    $message .= "A confirmation has been sent to " . $job->email . ". Please check your email and click on the confirmation link before the job is published.<br/><br/>";
                    $message .= "If your email address is not " . $job->email . " or if you think you have made an error, please hit the back button on your browser to correct and resubmit your form. <br/>";
                
                } catch (\Exception $e) {
                    $message .= $e;
                }
            }

            $success = 1;
        }
        
        return view('job.job_form', compact('user', 'message', 'states', 'educations', 'request', 'success'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/');
        }

        $states = States::all();

        $job = Jobs::where('id', $request->id)->first();

        $method = $request->method();
        $recaptcha = new ReCaptcha(env('DATA_SECRETKEY'));
        $response = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());
        $educations = array(
            "High School" => "High School",
            "2 Year Degree" => "2 Year Degree",
            "College Degree" => "College Degree",
            "Graduate Level" => "Graduate Level",
        );
        $message = "Please make the following corrections and submit again.";
        $success = 0;

        if ($method == "POST") {
            $valid_item = [
                'title' => 'required|min:5',
                'description' => 'required',
                'company' => 'required|min:5',
                'phone' => 'required|min:10',
                'email' => 'required',
                'city' => 'required|min:3',
                'state' => 'required',
                'zip' => 'required|min:5',
                'education' => 'required',
                'requirements' => 'required',
                'howtoapply' => 'required',
            ];

            if ($response->isSuccess()) {
                // reCAPTCHA verification successful, process the form submission
            }
            else{
                $valid_item['recaptcha-token'] = 'required';
            }

            $validated = $request->validate($valid_item);

            $job->title = $request->title;
            $job->description = $request->description;
            $job->state = $request->state;
            $job->city = $request->city;
            $job->zip = $request->zip;
            $job->education = $request->education;
            $job->rate_range = ($request->rate_range) ? $request->rate_range : "";
            $job->requirements = $request->requirements;
            $job->phone = $request->phone;
            $job->email = $request->email;
            $job->approved = 0;
            $job->email_verified = true;
            $job->company = $request->company;
            $job->howtoapply = $request->howtoapply;
            $job->user_id = $user->id;

            $job->save();

            $message = "Thank you for updating your job requirements. <br/><br/>";
            $message .= "Your job posting will be reviewed for approval within 1-2 business days. <br/>";
            $success = 1;
        }

        return view('job.job_edit', compact('user', 'request', 'job', 'states', 'educations', 'message', 'success'));
    }
}
