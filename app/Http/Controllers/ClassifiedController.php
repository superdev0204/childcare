<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Classifieds;
use App\Models\States;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use ReCaptcha\ReCaptcha;


class ClassifiedController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $states = States::where('country', 'US')
                        ->orderBy('state_name', 'asc')
                        ->get();
        
        $classifieds = Classifieds::where('approved', 1)
                                ->orderBy('created', 'desc')
                                ->limit(5)
                                ->get();

        return view('classified.index', compact('states', 'classifieds', 'user'));
    }

    public function state(Request $request)
    {
        $user = Auth::user();

        $statefile = request()->route()->parameter('state');
        
        $state = States::where('statefile', $statefile)->first();

        Cache::remember($statefile . '_state', 72000, function () use ($state) {
            return $state;
        });

        if (!$state) {
            return redirect('/');
        }

        $classifieds = Classifieds::where('approved', 1)
                                ->where('state', $state->state_code)
                                ->get();
        
        return view('classified.state', compact('user', 'classifieds', 'state'));
    }

    public function adDetails(Request $request)
    {
        $user = Auth::user();

        if (!$request->id) {
            return redirect('/classifieds');
        }

        /** @var Classified $classified */
        $classified = Classifieds::where('id', $request->id)->first();

        if (!$classified || $classified->approved < 0) {
            return redirect('/classifieds');
        }

        /** @var State $state */
        $state = States::where('state_code', $classified->state)->first();

        return view('classified.detail', compact('user', 'classified', 'state'));
    }

    public function newAd(Request $request)
    {
        $user = Auth::user();

        $states = States::all();

        $method = $request->method();
        $message = 'Please fill in the information and click submit.';
        $classified = [];

        if ($method == "POST") {

            $valid_item = [
                'summary' => 'required|min:5',
                'detail' => 'required',
                'name' => 'required|min:4',
                'phone' => 'required|min:10',
                'email' => 'required|email',
                'city' => 'required|min:3',
                'state' => 'required',
                'zip' => 'required|min:5',
            ];

            if($request->location){
                $valid_item['location'] = 'required|min:3';
            }

            if(!$user){
                $recaptcha = new ReCaptcha(env('DATA_SECRETKEY'));
                $response = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());

                if ($response->isSuccess()) {
                    // reCAPTCHA verification successful, process the form submission
                }
                else{
                    $valid_item['recaptcha-token'] = 'required';
                }
            }

            $validated = $request->validate($valid_item);

            $classified = Classifieds::create([
                'summary' => $request->summary,
                'detail' => $request->detail,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'approved' => 0,
                'pricing' => ($request->pricing) ? $request->pricing : '',
                'additionalInfo' => ($request->additionalInfo) ? $request->additionalInfo : '',
                'created' => new \DateTime(),
                'email_verified' => false,
                'user_id' => 0,
            ]);

            if($user){
                $classified->user_id = $user->id;
                $classified->email_verified = true;
                $classified->save();

                $message = 'Thank you for posting your classified ads. <br/>Your ad posting will be reviewed for approval within 1-2 business days. <br/>';
            }
            else {
                try {
                    $link = $request->getSchemeAndHttpHost() . '/classifieds/verifyad?id=' . $classified->id . '&e=' . $classified->email;
                    $message = 'Hello, <br /><br />';
                    $message .= 'Thank you for posting the ad' . $classified->summary . ' on ChildcareCenter.us.<br /><br /> ';
                    $message .= 'To verify that you are the person submitted the classified ad ' . $classified->summary . ', please follow this link:<br /><br />';
                    $message .= '<a href="' . $link . '">' . $link . '</a><br /><br />';
                    $message .= 'If you are unable to click on the link above, copy and paste the full link into your web browser\'s address bar and press enter. <br /><br />';
                    $message .= 'Thanks sincerely<br />';
                    
                    $data = array(
                        'from_name' => config('mail.from.name'),
                        'from_email' => config('mail.from.address'),
                        'subject' => 'Classified posting: ' . $classified->summary,
                        'message' => $message,
                    );
    
                    Mail::to($classified->email)->send(new SendEmail($data));

                    $message = 'Thank you for posting your ad with us. <br/><br/>';
                    $message .= 'A confirmation has been sent to ' . $classified->email . '. Please check your email and click on the confirmation link before the job is published.<br/><br/>';
                    $message .= 'If your email address is not ' . $classified->email . ' or if you think you have made an error, please hit the back button on your browser to correct and resubmit your form. <br/>';
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                }
            }
        }

        return view('classified.new_ad', compact('user', 'message', 'states', 'classified'));
    }

    public function verifyAd(Request $request)
    {
        $user = Auth::user();

        if (!$request->id || $request->e) {
            return redirect('/');
        }

        /** @var Classified $classified */
        $classified = Classifieds::where('id', $request->id)
                                ->where('email', $request->e)
                                ->first();

        if (!$classified) {
            return redirect('/');
        }

        $classified->email_verified = true;
        $classified->save();

        $message = 'Thank you for verify your email address.  Your classified posting will be reviewed for approval within 1-2 business days.';

        return view('classified.verify', compact('user', 'message'));
    }
}
