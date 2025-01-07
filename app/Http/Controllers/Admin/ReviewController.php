<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Facility;
use App\Models\Reviews;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            if ($request->pid) {
                $provider = Facility::where('id', $request->pid)->first();
                $reviews = Reviews::where('facility_id', $provider->id)
                                ->whereIn('approved', [0, 1])
                                ->get();
    
                return view('admin.review.index', compact('provider', 'reviews', 'user'));
            } else {
                $query = Reviews::orderBy('review_date', 'asc');
    
                if ($request->status == 1) {
                    $query->whereIn('approved', [0, 1]);
                } elseif (!$request->status) {
                    $query->where('approved', 0);
                }
    
                if ($request->pname) {
                    $query->where('facility_name', 'like', '%' . $request->pname . '%');
                }
    
                if ($request->ip_address) {
                    $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
                }
    
                if ($request->rating) {
                    $query->where('rating', (int) $request->rating);
                }
    
                if ($request->email) {
                    $query->where('email', $request->email);
                }
                
                $reviews = $query->get();
            }
            
            return view('admin.review.index', compact('reviews', 'user'));
        }
        else{
            return redirect('/user/login');
        }
    }

    public function find(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            $queryParams = $request->all();            
            unset($queryParams['search']);
            $queryParams = array_filter($queryParams);
            $message = '';
            $rating = [1 => '1 star', 2 => '2 star', 3 => '3 star', 4 => '4 star', 5 => '5 star'];

            if (empty($queryParams)) {
                $message = 'Please enter a search criteria!';
                return view('admin.review.find', compact('message', 'user', 'rating', 'request'));
            }
            
            if ($request->id && $request->pid) {
                $reviews = Reviews::where('id', $request->id)
                                ->where('facility_id', $request->pid)
                                ->get();
            } else {
                $query = Reviews::orderBy('review_date', 'asc');
                if ($request->status && $request->status != "-1") {
                    $query->where('approved', $request->status);
                }
    
                if ($request->pid) {
                    $query->where('facility_id', $request->pid);
                }
    
                if ($request->pname) {
                    $query->where('facility_name', 'like', '%' . $request->pname . '%');
                }
    
                if ($request->ip_address) {
                    $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
                }
    
                if ($request->rating) {
                    $query->where('rating', $request->rating);
                }
    
                if ($request->email) {
                    $query->where('email', $request->email);
                }

                $reviews = $query->get();
            }
    
            if (empty($reviews)) {
                $message = 'No results found!';
            }
    
            if (count($reviews) >= 20) {
                $message = 'Too many results.  Please limit your search!';
            }
            
            return view('admin.review.find', compact('reviews', 'message', 'user', 'rating', 'request'));
        }
        else{
            return redirect('/user/login');
        }
    }

    public function approve(Request $request)
    {
        if (!$request->isMethod('post')){

            return response()->json(['error' => true, 'data' => []], 404);
        }

        /** @var Review $review */
        $review = Reviews::where('id', $request->id)->first();

        if (!$review) {
            return redirect('/admin');
        }
        
        $provider = $review->provider;
        $review->approved = 1;
        $review->save();
        
        // update avg rating in provider
        $avgRating = $this->getAvgRating($provider);
        $provider->avg_rating = $avgRating;
        $provider->save();

        if ($request->backUrl) {
            return redirect($request->backUrl);
        }

        return redirect('/admin');
    }

    public function remove(Request $request)
    {
        if (!$request->isMethod('post')) {

            return response()->json(['error' => true, 'data' => []], 404);
        }
        
        /** @var Review $review */
        $review = Reviews::where('id', $request->id)->first();
        
        if (!$review) {
            return redirect('/admin');
        }
        
        $review->approved = -2;
        $review->save();
        
        // update avg rating in provider
        $provider  = $review->provider;
        $avgRating = $this->getAvgRating($provider);
        $provider->avg_rating = $avgRating;
        $provider->save();
        
        if ($request->backUrl) {
            return redirect($request->backUrl);
        }
        
        return redirect('/admin');
    }

    public function disapprove(Request $request)
    {
        if (!$request->isMethod('post')) {

            return response()->json(['error' => true, 'data' => []], 404);
        }

        /** @var Review $review */
        $review = Reviews::where('id', $request->id)->first();

        if (!$review) {
            return redirect('/admin');
        }

        $review->update(['approved' => -1]);

        if ($request->backUrl) {
            return redirect($request->backUrl);
        }

        return redirect('/admin');
    }

    public function response(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            $reviews = Reviews::whereNotNull('owner_comment')
                            ->whereNotNull('owner_comment_date')
                            ->where('owner_comment_approved', 0)
                            ->where('approved', 1)
                            ->orderBy('owner_comment_date', 'desc')
                            ->get();

            return view('admin.review.response', compact('reviews', 'user'));
        }
        else{
            return redirect('/user/login');
        }
    }

    public function approveResponse(Request $request)
    {
        if (!$request->isMethod('post')){

            return response()->json(['error' => true, 'data' => []], 404);
        }

        /** @var Review $review */
        $review = Reviews::where('id', $request->id)->first();

        if (!$review) {
            return redirect('/admin');
        }
        
        $review->owner_comment_approved = 1;
        $review->save();

        /* $provider = $review->getProvider();
        if (!$provider->getUser()) {
            $user = $this->reviewRepository->getBy($provider->getId());
            $provider->setUserId($user->get)
        } */
            
        if ($request->backUrl) {
            return redirect($request->backUrl);
        }

        return redirect('/admin');
    }

    public function disapproveResponse(Request $request)
    {
        if (!$request->isMethod('post')) {

            return response()->json(['error' => true, 'data' => []], 404);
        }

        /** @var Review $review */
        $review = Reviews::where('id', $request->id)->first();

        if (!$review) {
            return redirect('/admin');
        }

        $review->owner_comment_approved = -1;
        
        $review->save();

        if ($request->backUrl) {
            return redirect($request->backUrl);
        }

        return redirect('/admin');
    }

    public function getAvgRating($facility)
    {
        $avgRating = Reviews::where('approved', '>=', 1)
                            ->where('facility_id', $facility->id)
                            ->groupBy('facility_id')
                            ->avg('rating');
        
        $avgRating = ($avgRating) ? round($avgRating/0.5) * 0.5 : null;
        return $avgRating;
    }
}
