<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Resume;

class ResumeController extends Controller
{
    public function index(Request $request)
    {
        return redirect('/admin');
    }

    public function approve(Request $request)
    {
        if (!$request->isMethod('post')){

            return response()->json(['error' => true, 'data' => []], 404);
        }

        /** @var Resume $resume */
        $resume = Resume::where('id', $request->id)->first();

        if (!$resume) {
            return redirect('/admin');
        }

        $resume->approved = 1;

        $resume->save();

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

        /** @var Resume $resume */
        $resume = Resume::where('id', $request->id)->first();

        if (!$resume) {
            return redirect('/admin');
        }

        $resume->approved = -2;

        $resume->save();

        if ($request->backUrl) {
            return redirect($request->backUrl);
        }

        return redirect('/admin');
    }
}
