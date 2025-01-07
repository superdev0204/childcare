<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Mail\SendEmail;
use ReCaptcha\ReCaptcha;
use Illuminate\Support\Facades\Auth;
use App\Models\Facility;
use App\Models\Reviews;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $method = $request->method();
        
        if (!$user) {
            return redirect('/user/login');
        }
        
        $providerId = $request->pid;

        if (!$providerId) {
            return redirect('/');
        }

        $provider = Facility::where('id', $providerId)->first();

        $reviews = Reviews::where('facility_id', $provider->id)
                            ->where('approved', 1)
                            ->orderBy('review_date', 'desc')
                            ->get();

        return view('review.index', compact('user', 'provider', 'reviews'));
    }

    public function new(Request $request)
    {
        $user = Auth::user();
        
        $method = $request->method();
        
        $providerId = $request->pid ?: $request->pid;

        if (!$providerId) {
            return redirect('/');
        }

        /** @var Facility $provider */
        $provider = Facility::where('id', $providerId)->first();
        if (is_null($provider)) {
            return redirect('/');
        }

        // if ($this->params('iframe')) {
        //     $viewModel->setTemplate('application/review/partial/form')
        //         ->setTerminal(true);
        // }

        $message = 'Please complete the review form below and submit';
        $review = [];
        $experience = ["I have used this provider for more than 6 months" => "I have used this provider for more than 6 months", 
            "I have used this provider for less than 6 months" => "I have used this provider for less than 6 months", 
            "I have toured this provider's facility, but have not used its services" => "I have toured this provider's facility, but have not used its services", 
            "I am the owner" => "I am the owner", 
            "I am an employee" => "I am an employee", 
            "Other" => "Other"
        ];
        $rating = [1 => '1 star', 2 => '2 star', 3 => '3 star', 4 => '4 star', 5 => '5 star'];

        if ($method == "POST") {
            $recaptcha = new ReCaptcha(env('DATA_SECRETKEY'));
            $response = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());

            $withErrors = [];
            if(!$request->experience){
                $withErrors['experience'] = 'The experience field is required';
            }

            if(!$request->rating){
                $withErrors['rating'] = 'The rating field is required';
            }

            if(!$request->comments){
                $withErrors['comments'] = 'The comments field is required';
            }

            if ($response->isSuccess()) {
                // reCAPTCHA verification successful, process the form submission
            }
            else{
                $withErrors['recaptcha-token'] = 'The recaptcha-token field is required.';
            }

            if(!$request->email){
                $withErrors['email'] = 'The email field is required';
            }

            if(!$request->name){
                $withErrors['name'] = 'The name field is required';
            }

            if($request->name && strlen($request->name) < 3){
                $withErrors['name'] = 'The name field must be at least 3 characters.';
            }

            if(count($withErrors) > 0){
                return view('review.new', compact('user', 'provider', 'rating', 'experience', 'message', 'review', 'request'))->withErrors($withErrors);
            }

            if(preg_match("/mail.ru/i",$request->email) ||
                preg_match("/mal.ru/i",$request->email) ||
                substr_count($request->email,".") > 3 ||
                substr_count($request->comments,"http") > 3 ||
                preg_match("/(218.10.17.41|91.207.4.242)/",$_SERVER['REMOTE_ADDR']) ||
                preg_match("/\b(viagra|levitra|cialis|marijuana)\b/i",$request->comments)) {
                return view('review.new', compact('user', 'provider', 'rating', 'experience', 'message', 'review', 'request'));
            }

            if (preg_match("/(http:\/\/|href=)/i",$request->comments)) {
                if (preg_match("/(clearance|outlet|webnode|Cocaine|zithromax|boots|loan|watches|jersey|phone|shoe|prescription|coach)/i",$request->comments) ||
                    preg_match("/(replica|vuitton|imitation|coupon|fake|generic|goose|film|cocktail|cartridge|gold|nike|skin|gucci)/i",$request->comments) ||
                    preg_match("/(karen|millen|co.uk|purse|webdesign|bags|marque)/i",$request->comments) ||
                    preg_match("/(moisture|herbal|fragrance|exfoliate|chanel|wedding|gowns|furniture|Simferopol|beach|moisturize)/i",$request->comments) ||
                    preg_match("/(arthritis|shopping|handbag|vaporizer|costume|snoring|movie|sex|blogspot|abercrombie|\?\?\?\?\?)/i",$request->comments)) {
                    return view('review.new', compact('user', 'provider', 'rating', 'experience', 'message', 'review', 'request'));
                }
            }

            if (preg_match("/\.ru/i",$request->email) &&
                preg_match("/\?\?\?\?\?/i",$request->comments)) {
                return view('review.new', compact('user', 'provider', 'rating', 'experience', 'message', 'review', 'request'));
            }
            
            $review = Reviews::create([
                'facility_id' => $providerId,
                'email' => $request->email,
                'review_by' => $request->name,
                'experience' => $request->experience,
                'rating' => $request->rating,
                'comments' => $request->comments,
                'approved' => 0,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'facility_name' => $provider->name,
                'facility_filename' => $provider->filename,
                'owner_comment' => '',
                'helpful' => 0,
                'nohelp' => 0,
                'email_verified' => 0,
            ]);

            if (!preg_match("/http:\/\//i", $review->comments)) {
                try {
                    $message = "
                        Hello, <br/><br/>
                        We received the following review for {$review->facility_name}:<br/>
                        ----------<br/>
                        {$review->comments}<br />
                        ----------<br/><br/>
                        To verify that you are the person submitted the review above, please follow this link:<br/><br/>
                        <a href='{$request->getSchemeAndHttpHost()}/review/verify/{$review->id}/{$review->facility_id}'>{$request->getSchemeAndHttpHost()}/review/verify/{$review->id}/{$review->facility_id}</a><br/><br/>
                        If you are unable to click on the link above, copy and paste the full link into your web browser's address bar and press enter. <br/><br/>
                        Thanks sincerely
                    ";

                    $data = array(
                        'from_name' => config('mail.from.name'),
                        'from_email' => config('mail.from.address'),
                        'subject' => 'Your Review for ' . $review->facility_name,
                        'message' => $message,
                    );

                    Mail::to($request->email)->send(new SendEmail($data));

                    $message = 'Thank you for your comment. <br/><br/>';
                    $message .= 'A confirmation has been sent to ' . $review->email . '. Please check your email and click on the confirmation link before the review is published.<br/><br/>';
                    $message .= 'If your email address is not ' . $review->email . ' or if you think you have made an error, please hit the back button on your browser to correct and resubmit your form. <br/>';
                    $message .= '<br/> <a href=\'/provider_detail/' . $provider->filename . '\'>Return to listing details</a>';
                
                } catch (\Exception $e) {
                    $message = $e;
                }
            }
        }

        return view('review.new', compact('user', 'provider', 'rating', 'experience', 'message', 'review', 'request'));
    }

    public function verify(Request $request)
    {
        $user = Auth::user();

        /** @var Review $review */
        $review = Reviews::where('facility_id', request()->route()->parameter('pid'))
                        ->where('id', request()->route()->parameter('id'))
                        ->first();

        if (!$review) {
            return redirect('/');
        }

        $review->email_verified = true;

        $review->save();

        $provider = $review->provider;

        return view('review.verify', compact('user', 'provider'));
    }

    public function response(Request $request)
    {
        $user = Auth::user();

        $method = $request->method();
        
        /** @var Review $review */
        $review = Reviews::where('id', request()->route()->parameter('id'))->first();

        if (!$review) {
            return redirect('/');
        }
        if (!$user) {
            return redirect('/user/login');
        } 
        
        $reviewProvider = $review->provider;
        $userProvider = $user->provider;
        
        try {
            $owner = $review->provider->user;
        } catch (ModelNotFoundException $e) {
            if ($userProvider->id != $reviewProvider->id) {
                return redirect('/');
            }
        }

        if (!$user || !$user->provider){
            return redirect('/user/login');
        }
        if ($owner && $user->id != $owner->id) {
            return redirect('/user/login');
        }
        if (!$owner && $userProvider->id != $reviewProvider->id) {
            return redirect('/user/login');
        }
        
        if ($method == "POST") {
            $valid_item = [
                'owner_comment' => 'required',
            ];

            $validated = $request->validate($valid_item);

            $review->owner_comment = $request->owner_comment;
            $review->owner_comment_date = new \DateTime();

            $review->save();

            return redirect()->route('review_owner_response', ['id' => $review->id]);
        }

        return view('review.response', compact('user', 'review', 'request'));
    }

    public function vote(Request $request)
    {
        $user = Auth::user();

        $isHelpful = $request->is_helpful;

        if (!isset($isHelpful)) {
            return response()->json([]);
        }

        /** @var Review $review */
        $review = Reviews::where('id', request()->route()->parameter('id'))->first();

        if (!$review) {
            return response()->json([]);
        }

        $reviewsVoted = $request->cookie('reviews_voted') ? unserialize($request->cookie('reviews_voted')) : [];

        if (isset($reviewsVoted[$review->id]) && $reviewsVoted[$review->id] == $isHelpful) {
            return response()->json([
                'error' => sprintf('You have already voted with "%s"', $isHelpful ? 'Yes' : 'No')
            ]);
        }

        if ($isHelpful) {
            $review->helpful = $review->helpful + 1;
            // Get back the "No" choice
            if (isset($reviewsVoted[$review->id]) && !$reviewsVoted[$review->id]) {
                $review->nohelp = $review->nohelp - 1;
            }
        } else {
            $review->nohelp = $review->nohelp + 1;
            // Get back the "Yes" choice
            if (isset($reviewsVoted[$review->id]) && $reviewsVoted[$review->id]) {
                $review->helpful = $review->helpful - 1;
            }
        }

        $review->save();

        $reviewsVoted = $request->cookie('reviews_voted') ? unserialize($request->cookie('reviews_voted')) : [];
        $reviewsVoted[$review->id] = (int) $isHelpful;

        $host = request()->getHost();
        $cookieDomain = '.' . str_replace('www.', '', $host);

        $response = new Response();
        $cookie = cookie('reviews_voted', serialize($reviewsVoted), 60 * 24 * 365, '/', $cookieDomain);
        $response->withCookie($cookie);

        $totalVotes = $review->helpful + $review->nohelp;
        if (!$totalVotes) {
            $review->helpful_text = '';
        } else {
            $review->helpful_text = sprintf('%s out of %s think this review is helpful', $review->helpful, $totalVotes);
        }
        
        return response()->json([
            'helpfulText' => $review->helpful_text,
            'isHelpful' => $isHelpful,
            'reviewId' => $review->id
        ]);
    }
}
