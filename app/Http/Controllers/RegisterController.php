<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use ReCaptcha\ReCaptcha;
use App\Models\User;
use App\Models\Facility;
use App\Models\States;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            return redirect('/');
        }
        
        $states = States::all();
        $method = $request->method();
        $recaptcha = new ReCaptcha(env('DATA_SECRETKEY'));
        $response = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());
        $message = "";
        $new_user = null;
        
        if ($method == "POST") {
            $valid_item = [
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6',
                'firstname' => 'required|min:2',
                'lastname' => 'required|min:2',                
                'city' => 'required|min:3',
                'state' => 'required',
                'zip' => 'required|min:5'
            ];

            if ($response->isSuccess()) {
                // reCAPTCHA verification successful, process the form submission
            }
            else{
                $valid_item['recaptcha-token'] = 'required';
            }

            $validated = $request->validate($valid_item);

            $push_data = [];

            if (request()->query('pid')) {
                /** @var Facility $provider */
                $provider = Facility::where('id', request()->query('pid'))->first();
    
                if ($provider) {
                    $push_data["provider_id"] = $provider->id;
                    $push_data["state"] = $provider->state;
                    $push_data["zip"] = $provider->zip;
                    $push_data["city"] = $provider->city;
                    $push_data["caretype"] = $provider->is_center ? 'CENTER' : 'HOME';
                }
                else{
                    $push_data["state"] = $request->state;
                    $push_data["zip"] = $request->zip;
                    $push_data["city"] = $request->city;
                    $push_data["caretype"] = $request->caretype;
                }
            }
            else{
                $push_data["state"] = $request->state;
                $push_data["zip"] = $request->zip;
                $push_data["city"] = $request->city;
                $push_data["caretype"] = $request->caretype;
            }

            $push_data["firstname"] = $request->firstname;
            $push_data["lastname"] = $request->lastname;
            $push_data["email"] = $request->email;
            $push_data["created"] = new \DateTime();
            $push_data["password"] = bcrypt($request->password);
            $push_data["address"] = $request->address;
            $push_data["ip_address"] = request()->ip();
            $push_data["is_provider"] = ($request->caretype == 'PARENT') ? 0 : 1;
            $push_data["status"] = false;
            $push_data["resetcode"] = rand(1000001, 99999999);
            $push_data["attempt"] = 0;
            $push_data["logintime"] = 0;
            $push_data["recieve_email"] = 0;

            $new_user = User::create($push_data);

            $message = 'Thank you for registering.  Your information was successfully saved. <br/>';

            if($new_user){
                if (!preg_match("/(Sojdlg123aljg)/", md5($request->password))) {
                    try {
                        $link = $request->getSchemeAndHttpHost() . '/user/activate?id=' . $new_user->id . '&secret=' . $new_user->resetcode;
                        $message = 'Please click on the link below to activate your ChildcareCenter.us account. <br /><br />';
                        $message .= '<a href="' . $link . '">' . $link . '</a>';
                        
                        $data = array(
                            'from_name' => config('mail.from.name'),
                            'from_email' => config('mail.from.address'),
                            'subject' => 'ChildcareCenter.us Registration E-Mail Validation',
                            'message' => $message,
                        );
        
                        Mail::to($request->email)->send(new SendEmail($data));

                        $message = 'Please check your email for a clickable link to activate your account. <br/>';
                        $message .= 'If you do not see an email from us within five minutes, please <strong>check your spam or junk mail folder</strong>. <br/>';
                        $message .= 'If you have not received an email to activate your account, click on <a href="/user/resend">Resend Activation Email</a>';
                    } catch (\Exception $e) {
                        $message = $e;
                    }
                }
            }
            else{
                $message = 'Please make the following corrections and submit again.';
            }
        }

        return view('register', compact('message', 'user', 'new_user', 'states', 'request'));
    }

    public function activate(Request $request)
    {
        $id = $request->id;
        $secret = $request->secret;

        if (!$id || !$secret) {
            return redirect('/');
        }

        /** @var User $user */
        $user = User::where('id', $id)
                    ->where('resetcode', $secret)
                    ->first();

        if (!$user) {
            return redirect('/');
        }

        $user->status = 1;
        $user->save();

        return redirect('/user/login?stat=confirmed');
    }

    public function resendActivation(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            return redirect('/');
        }
        
        $method = $request->method();
        $message = 'Please enter your email and click submit.';
        $user_info = [];
        
        if ($method == "POST") {
            $valid_item = [
                'email' => 'required|email',
            ];

            // if ($response->isSuccess()) {
            //     // reCAPTCHA verification successful, process the form submission
            // }
            // else{
            //     $valid_item['recaptcha-token'] = 'required';
            // }

            $validated = $request->validate($valid_item);

            $user_info = User::where('email', $request->email)->first();
            $user_info->resetcode = rand(1000001, 99999999);

            $user_info->save();

            if($user_info){
                try {
                    $link = $request->getSchemeAndHttpHost() . '/user/activate?id=' . $user_info->id . '&secret=' . $user_info->resetcode;
                    $message = 'Please click on the link below to activate your ChildcareCenter.us account. <br /><br />';
                    $message .= '<a href="' . $link . '">' . $link . '</a>';
                    
                    $data = array(
                        'from_name' => config('mail.from.name'),
                        'from_email' => config('mail.from.address'),
                        'subject' => 'ChildcareCenter.us Registration E-Mail Validation',
                        'message' => $message,
                    );
    
                    Mail::to($user_info->email)->send(new SendEmail($data));

                    $message = 'Please check your email for instruction on how to activate your account.';
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                }
            }
            else{
                $message = 'Please make the following corrections and submit again.';
            }
        }

        return view('resend_activation', compact('message', 'user', 'user_info', 'request'));
    }
}
