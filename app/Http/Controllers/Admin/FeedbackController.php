<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Testimonials;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            $feedbacks = Testimonials::where('approved', 0)
                                    ->orderBy('date', 'desc')
                                    ->get();

            return view('admin.feedback', compact('feedbacks', 'user'));
        }
        else{
            return redirect('/user/login');
        }
    }

    public function spam(Request $request)
    {
        if (!$request->isMethod('post')){

            return response()->json(['error' => true, 'data' => []], 404);
        }

        /** @var Testimonials $feedback */
        $feedback = Testimonials::where('id', $request->id)->first();
        $feedback->approved = -99;

        $feedback->save();

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

        /** @var Testimonials $feedback */
        $feedback = Testimonials::where('id', $request->id)->first();
        $feedback->approved = -1;

        $feedback->save();

        if ($request->backUrl) {
            return redirect($request->backUrl);
        }

        return redirect('/admin');
    }
}
