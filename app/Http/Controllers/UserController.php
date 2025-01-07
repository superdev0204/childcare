<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\States;
use App\Models\Jobs;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/');
        }

        $jobs = Jobs::where('email', $user->email)
                    ->orderBy('created', 'desc')
                    ->limit(10)
                    ->get();

        return view('user.profile', compact('user', 'jobs'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/');
        }

        $states = States::all();

        $method = $request->method();
        $successMsg = "";
        $errorMsg = "";

        if ($method == "POST") {
            $valid_item = [
                'firstname' => 'required|min:2',
                'lastname' => 'required|min:2',                
                'city' => 'required|min:3',
                'state' => 'required',
                'zip' => 'required|min:5'
            ];

            $validated = $request->validate($valid_item);
            
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->city = $request->city;
            $user->state = $request->state;
            $user->zip = $request->zip;

            $user->save();

            $successMsg = 'Update user information success';
        }

        return view('user.update', compact('user', 'states', 'successMsg', 'errorMsg'));
    }

    public function password(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/');
        }

        $method = $request->method();
        $successMsg = "";
        $errorMsg = "";

        if ($method == "POST") {
            $valid_item = [
                'currentPassword' => 'required|min:6',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6',
            ];

            $validated = $request->validate($valid_item);

            if ( Hash::check($request->currentPassword, $user->password) ) {
                $user->password = $request->password;
                $user->save();
                $successMsg = "Update password success";
            } else {
                $errorMsg = "Current password not match";
            }
        }

        return view('user.password', compact('user', 'successMsg', 'errorMsg'));
    }
}
