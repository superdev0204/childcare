<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Classifieds;

class ClassifiedController extends Controller
{
    public function approve(Request $request)
    {
        $user = Auth::user();
        $method = $request->method();
        
        $id = $request->id;

        if (!$method == "POST" || !$id) {
            return redirect('/admin');
        }

        /** @var Classified $classified */
        $classified = Classifieds::where('id', $id)->first();

        if (!$classified) {
            return redirect('/admin');
        }

        $classified->approved = 1;

        $classified->save();

        return redirect('/admin');
    }

    public function disapprove(Request $request)
    {
        $user = Auth::user();
        $method = $request->method();

        $id = $request->id;

        if (!$method == "POST" || !$id) {
            return redirect('/admin');
        }

        /** @var Classified $classified */
        $classified = Classifieds::where('id', $id)->first();

        if (!$classified) {
            return redirect('/admin');
        }

        $classified->approved = -2;

        $classified->save();

        return redirect('/admin');
    }
}
