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
use App\Models\Resume;
use App\Models\States;
use App\Models\Cities;
use App\Models\Careerbuilderlog;

class ResumeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $resumes = Resume::where('approved', 1)
                        ->orderBy('created', 'desc')
                        ->limit(10)
                        ->get();
        
        $states = States::where('country', 'US')->orderBy('state_name', 'asc')->get();

        return view('resume.resume', compact('user', 'states', 'resumes'));
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

        $resumes = Resume::where('approved', 1)
                        ->where('state', $state->state_code)
                        ->get();
        
        return view('resume.resume_state', compact('user', 'resumes', 'state'));
    }

    public function view(Request $request)
    {
        $user = Auth::user();
        
        /** @var Jobs $job */
        $resume = Resume::where('id', $request->id)->first();
        
        if (!$resume) {
            return redirect('/');
        }
        
        /** @var State $state */
        $state = States::where('state_code', $resume->state)->first();

        return view('resume.resume_view', compact('user', 'resume', 'state'));
    }

    public function verify(Request $request)
    {
        $user = Auth::user();

        if (!$request->id || !$request->e) {
            return redirect('/');
        }

        /** @var Resume $resume */
        $resume = Resume::where('id', $request->id)
                        ->where('email', $request->e)
                        ->first();

        if (!$resume) {
            return redirect('/');
        }

        $resume->email_verified = true;
        $resume->save();

        $message = 'Thank you for verify your email address.  Your job posting will be reviewed for approval within 1-2 business days.';

        return view('resume.resume_verify', compact('user', 'resume', 'message'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        $states = States::all();
        
        $method = $request->method();
        $recaptcha = new ReCaptcha(env('DATA_SECRETKEY'));
        $response = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());
        $educationLevels = array(
            "HS" => "High School",
            "SC" => "Some College",
            "CL" => "College",
        );
        $message = "Please make the following corrections and submit again.";
        $success = 0;

        if ($method == "POST") {
            $valid_item = [
                'name' => 'required|min:5',
                'phone' => 'required|min:10',
                'email' => 'required',
                'city' => 'required|min:3',
                'state' => 'required',
                'zip' => 'required|min:5',
                'objective' => 'required',
                'skillsCertification' => 'required',
                'educationLevel' => 'required',
                'school' => 'required|min:5',
            ];

            if ($request->position) {
                $valid_item['position'] = 'required|min:5';
            }

            if ($request->major) {
                $valid_item['major'] = 'required|min:5';
            }

            if ($response->isSuccess()) {
                // reCAPTCHA verification successful, process the form submission
            }
            else{
                $valid_item['recaptcha-token'] = 'required';
            }

            $validated = $request->validate($valid_item);
            
            $push_data = [
                'name' => $request->name,
                'position' => ($request->position) ? $request->position : "",
                'objective' => ($request->objective) ? $request->objective : "",
                'rate_range' => ($request->rate_range) ? $request->rate_range : "",
                'state' => $request->state,
                'city' => $request->city,
                'zip' => $request->zip,
                'phone' => $request->phone,
                'email' => $request->email,
                'experience' => ($request->experience) ? $request->experience : "",
                'skillsCertification' => $request->skillsCertification,
                'created' => new \DateTime(),
                'approved' => 0,
                'educationLevel' => ($request->educationLevel) ? $request->educationLevel : "",
                'school' => ($request->school) ? $request->school : "",
                'major' => ($request->major) ? $request->major : "",
                'additionalInfo' => ($request->additionalInfo) ? $request->additionalInfo : "",
                'email_verified' => false,
            ];

            $resume = Resume::create($push_data);

            $message = "Thank you for posting your resume. <br/><br/>";

            $mailMessage = "Hello, <br/><br/>";
            $mailMessage .= "Thank you for posting your resume on childcarecenter.us/jobs.<br/><br/> ";
            $mailMessage .= "To verify that you are the person submitted the resume , please follow this link:<br/><br/>";
            $mailMessage .= $request->getSchemeAndHttpHost() . "/resumes/verify?id=" . $resume->id . "&e=" . $resume->email . "<br/><br/>";
            $mailMessage .= "If you are unable to click on the link above, copy and paste the full link into your web browser's address bar and press enter. <br/><br/>";
            $mailMessage .= "Thanks sincerely<br/>";
            
            try {
                $data = array(
                    'from_name' => config('mail.from.name'),
                    'from_email' => config('mail.from.address'),
                    'subject' => 'Resume posting for ' . $resume->name,
                    'message' => $mailMessage,
                );

                Mail::to($resume->email)->send(new SendEmail($data));

                $message .= "A confirmation has been sent to " . $resume->email . ". Please check your email and click on the confirmation link before the resume is published.<br/><br/>";
                $message .= "If your email address is not " . $resume->email . " or if you think you have made an error, please hit the back button on your browser to correct and resubmit your form. <br/>";
            
            } catch (\Exception $e) {
                $message .= $e;
            }

            $success = 1;
        }
        
        return view('resume.resume_form', compact('user', 'message', 'states', 'educationLevels', 'request', 'success'));
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
