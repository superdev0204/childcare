<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Facility;
use App\Models\Facilitylog;
use App\Models\Classifieds;
use App\Models\Reviews;
use App\Models\Iptracker;

class IndexController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            $providers = Facility::where('approved', 0)
                                ->orderBy('name', 'asc')
                                ->get();

            $providerLogs = Facilitylog::with('provider')->where('approved', 0)
                                        ->orderBy('name', 'asc')
                                        ->get();
            
            $providerLogs = $providerLogs->filter(function ($log) {
                return !is_null(optional($log->provider)->id);
            });         

            $classifieds = Classifieds::where('approved', 0)->get();

            $reviews = Reviews::where('approved', 0)
                            ->where('email_verified', 1)
                            ->orderBy('review_date', 'desc')
                            ->get();

            $ips = Iptracker::where('total_count', '>=', 1000)
                            ->where('ludate', '>=', now()->subDay())
                            ->orderBy('ludate', 'desc')
                            ->get();

            return view('admin.index', compact('providers', 'providerLogs', 'classifieds', 'reviews', 'ips', 'user'));
        }
        else{
            return redirect('/user/login');
        }
    }
}
