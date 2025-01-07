<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use ReCaptcha\ReCaptcha;
use App\Mail\SendEmail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function showLinkRequestForm()
    {
        return view('passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $method = $request->method();
        $recaptcha = new ReCaptcha(env('DATA_SECRETKEY'));
        $response = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());
        $message = 'Please enter your email and click submit.';
        $user = null;

        if ($method == "POST") {
            $valid_item = [
                'email' => 'required|email'
            ];

            if ($response->isSuccess()) {
                // reCAPTCHA verification successful, process the form submission
            }
            else{
                $valid_item['recaptcha-token'] = 'required';
            }
            
            $validated = $request->validate($valid_item);

            $email = $request->email;

            /** @var User $user */
            $user = User::where('email', $email)->first();
            
            if($user){
                $user->resetcode = rand(1000001, 99999999);
                $user->save();

                try {
                    $link = $request->getSchemeAndHttpHost() . "/user/pwdreset/{$user->resetcode}/{$user->id}";
                    $message = 'Please click on the link below to reset your password. <br /></br />';
                    $message .= '<a href="' . $link . '">' . $link . '</a>';
                    
                    $data = array(
                        'from_name' => config('mail.from.name'),
                        'from_email' => config('mail.from.address'),
                        'subject' => 'Childcarecenter.us Account password reset',
                        'message' => $message,
                    );

                    Mail::to($user->email)->send(new SendEmail($data));
                    
                    $message = 'Please check your email for instruction on how to reset password.';
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                }
            } else {
                $message = 'Please make the following corrections and submit again.';
            }
        }

        return view('passwords.email', compact('message', 'user', 'request'));
    }
}
