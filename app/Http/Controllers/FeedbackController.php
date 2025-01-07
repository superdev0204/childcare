<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use ReCaptcha\ReCaptcha;
use Illuminate\Support\Facades\Auth;
use App\Models\Testimonials;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $method = $request->method();
        $recaptcha = new ReCaptcha(env('DATA_SECRETKEY'));
        $response = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());
        $message = 'Please help us improve our website to make it more useful to you by leaving your feedback and suggestions below.';
        
        $testimonials = Testimonials::where('approved', 1)
                                    ->orderBy('date', 'desc')
                                    ->limit(5)
                                    ->get();        

        if ($method == "POST") {

            $valid_item = [
                'name' => 'required|min:4',
                'email' => 'required|email',
            ];

            if($request->location){
                $valid_item['location'] = 'required|min:3';
            }

            if ($response->isSuccess()) {
                // reCAPTCHA verification successful, process the form submission
            }
            else{
                $valid_item['recaptcha-token'] = 'required';
            }

            $validated = $request->validate($valid_item);

            $testimonial = Testimonials::create([
                'comments' => 'suggestion',
                'approved' => 0,
                'email_verified' => isset($user->caretype),
                'name' => $request->name,
                'location' => $request->location,
                'date' => new \DateTime(),
                'email' => $request->email,
                'pros' => ($request->pros) ? $request->pros : '',
                'cons' => ($request->cons) ? $request->cons : '',
                'suggestion' => ($request->suggestion) ? $request->suggestion : '',
            ]);

            $message = 'Thank you for your feedback. <br/>';
            
            return view('feedback', compact('user', 'testimonials', 'testimonial', 'message', 'request'));
        }

        return view('feedback', compact('user', 'testimonials', 'message', 'request'));
    }
}
