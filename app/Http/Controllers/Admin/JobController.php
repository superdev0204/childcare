<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jobs;
use App\Models\Resume;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            $jobs = Jobs::where('approved', 0)->get();
            $resumes = Resume::where('approved', 0)->get();
            
            return view('admin.job', compact('jobs', 'resumes', 'user'));
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

        /** @var Job $job */
        $job = Jobs::where('id', $request->id)->first();

        if (!$job) {
            return redirect('/admin');
        }

        $job->approved = 1;

        $job->save();

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

        /** @var Job $job */
        $job = Jobs::where('id', $request->id)->first();

        if (!$job) {
            return redirect('/admin');
        }

        $job->approved = -2;

        $job->save();

        if ($request->backUrl) {
            return redirect($request->backUrl);
        }

        return redirect('/admin');
    }
}
