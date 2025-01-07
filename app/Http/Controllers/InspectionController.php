<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use ReCaptcha\ReCaptcha;
use Illuminate\Support\Facades\Auth;
use App\Models\Facility;
use App\Models\Reviews;
use App\Models\Inspections;

class InspectionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $method = $request->method();

        if (!$user) {
            return redirect('/provider');
        }

        $providerId = $request->pid;

        if (!$providerId) {
            /** @var Facility $provider */
            $provider = $user->provider;
        } else {
            /** @var Facility $provider */
            $provider = Facility::where('id', $providerId)->first();
        }

        if (!$provider) {
            return redirect('/provider');
        }

        $inspections = Inspections::where('facility_id', $provider->id)
                                ->orderBy('report_date', 'desc')
                                ->get();

        $reviews = Reviews::where('facility_id', $provider->id)
                            ->orderBy('review_date', 'desc')
                            ->get();

        return view('inspection_view', compact('user', 'provider', 'inspections', 'reviews'));
    }
}
