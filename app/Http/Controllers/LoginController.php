<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            if ($user->is_provider) {
                return redirect('/provider');
            } else {
                return redirect('/');
            }
        }

        $method = $request->method();
        $errorMessage = null;
        
        if ($method == "POST") {
            $withErrors = [];
            if(!$request->email){
                $withErrors['email'] = 'The email field is required';
            }
            
            if(!$request->password){
                $withErrors['password'] = 'The password field is required';
            }

            if(count($withErrors) > 0){
                return view('login', compact('request', 'errorMessage'))->withErrors($withErrors);
            }

            $valid_item = [
                'email' => 'required',
                'password' => 'required'
            ];
            $validated = $request->validate($valid_item);

            $check_user = User::where('email', $request->email)->first();

            if($check_user && strlen($check_user->password) === 32){
                if (!$check_user->status) {
                    $errorMessage = 'Your account has not been activated yet.  Please activate';
                    return view('login', compact('request', 'errorMessage'));
                }
                else{
                    if(md5($request->password) === $check_user->password){
                        $check_user->password = bcrypt($request->password);
                        $check_user->save();
                    }
                    else{
                        $errorMessage = 'Wrong login info';
                        return view('login', compact('request', 'errorMessage'));
                    }
                }
            }
            
            if (Auth::attempt($validated)) {
                $user = Auth::user();
                $user->login = new \DateTime();
                $user->logintime = ($user->logintime + 1);
                $user->attempt = 0;

                // if ($user->status) {
                $backUrl = request()->query('return_url');
                
                // set cookie
                $response = new Response();
                $cookie = Cookie::make('ezoic_no_cache', true, (\time() + (60 * 60 * 24 * 365)), '/', null, request()->secure(), true);
                $response->withCookie($cookie);

                if ($backUrl) {
                    return redirect($backUrl);
                }

                if ($user->caretype == 'ADMIN') {
                    return redirect('/admin');
                }

                if ($user->is_provider) {
                    return redirect('/provider');
                }

                return redirect('/');
                // }

                if (!$user->status) {
                    $errorMessage = 'Your account has not been activated yet.  Please activate';
                }
                // Auth::logout();
            }
            else{
                if ($check_user) {
                    $check_user->attempt = $check_user->attempt + 1;
                    $check_user->save();
                }

                $errorMessage = 'Wrong login info';
            }
        }

        return view('login', compact('request', 'errorMessage'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $cookieValue = $request->cookie('ezoic_no_cache');

        // Check if the 'ezoic_no_cache' cookie is set
        if (!is_null($cookieValue)) {
            $response = new Response();
            
            // Create a new cookie to unset the 'ezoic_no_cache' cookie
            $cookie = Cookie::forget('ezoic_no_cache');

            // Return the response with the cookie removed
            $response->withCookie($cookie);
        }
        
        return redirect('/');
    }
}
