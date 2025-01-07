<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Visitors;
use App\Models\Visitorsummary;

class VisitorController extends Controller
{
    public function visitor_counts(Request $request)
    {
        $user = Auth::user();
        $visitor_counts = Visitors::orderBy('date', 'desc')->paginate(100);

        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            return view('admin.visitor.visitor_counts', compact('user', 'visitor_counts'))->with('success', session('success'));
        }
        else{
            return redirect('/user/login?return_url='.request()->path());
        }
    }

    public function delete_visitor(Request $request)
    {
        $user = Auth::user();
        
        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            if(isset($request->vID) && !empty($request->vID)){
                $visitor_count = Visitors::find($request->vID);
                $visitor_count->delete();
            }
            return redirect('/admin/visitor_counts')->with('success', 'The visitor count deleted successfully');
        }
        else{
            return redirect('/')->with('error', 'Invalid credentials.');
        }
        
    }

    public function visitor_summary(Request $request)
    {
        $user = Auth::user();
        
        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            $dateYM = $request->dateYM ? $request->dateYM : date('Y-m');
            $visitor_summarys = Visitorsummary::where('date', 'like', $dateYM . '%')
                                            ->orderBy('date', 'desc')
                                            ->paginate(100);
            return view('admin.visitor.visitor_summary', compact('user', 'visitor_summarys', 'dateYM'))->with('success', session('success'));
        }
        else{
            return redirect('/user/login?return_url='.request()->path());
        }
    }

    public function delete_visitor_summary(Request $request)
    {
        $user = Auth::user();
        
        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            if(isset($request->vID) && !empty($request->vID)){
                $visitor_summary = Visitorsummary::find($request->vID);
                $visitor_summary->delete();
            }
            return redirect('/admin/visitor_summary')->with('success', 'The visitor summary deleted successfully');
        }
        else{
            return redirect('/')->with('error', 'Invalid credentials.');
        }
        
    }
}
