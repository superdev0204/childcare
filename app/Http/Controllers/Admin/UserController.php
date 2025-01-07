<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Facility;
use App\Models\User;
use App\Models\States;

class UserController extends Controller
{
    public function switch(Request $request)
    {
        $user = Auth::user();

        if ( !isset($request->id) ) {
            return redirect('/admin/user/find');
        }

        $user = User::where('id', $request->id)->first();
        if ($user === null) {
            return redirect('/admin/user/find?id=' . $request->id);
        }

        // Logout user
        Auth::logout();

        // Log in the new user
        Auth::login($user);

        // Redirect to the 'provider' route
        return redirect()->route('provider');
    }

    public function find(Request $request)
    {
        $user = Auth::user();
        $states = States::all();

        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            $queryParams = $request->all();            
            unset($queryParams['search']);
            $queryParams = array_filter($queryParams);
            $message = '';

            if (empty($queryParams)) {
                $message = 'Please enter a search criteria!';
                return view('admin.user', compact('message', 'user', 'states', 'request'));
            }

            $query = User::orderBy('created', 'asc');
            if ($request->id) {
                $query->where('id', $request->id);
            }

            if ($request->email) {
                $query->where('email', $request->email);
            }

            if ($request->firstname) {
                $query->where('firstname', 'like', '%' . $request->firstname . '%');
            }

            if ($request->lastname) {
                $query->where('lastname', 'like', '%' . $request->lastname . '%');
            }

            if ($request->city) {
                $query->where('city', $request->city);
            }

            if ($request->state) {
                $query->where('state', $request->state);
            }

            if ($request->zip) {
                $query->where('zip', $request->zip);
            }

            $users = $query->limit(20)->get();
            
            if (empty($users)) {
                $message = 'No results found!';
            }

            if (count($users) == 20) {
                $message = 'Too many results.  Please limit your search!';
            }

            return view('admin.user', compact('users', 'message', 'user', 'states', 'request'));
        }
        else{
            return redirect('/user/login');
        }
    }

    public function activate(Request $request)
    {
        if (!$request->isMethod('post')){

            return response()->json(['error' => true, 'data' => []], 404);
        }

        /** @var User $user */
        $user = User::where('id', $request->id)->first();

        if (!$user) {
            return redirect('/admin');
        }

        $user->status = 1;

        $user->save();

        if ($request->backUrl) {
            return redirect($request->backUrl);
        }

        return redirect('/admin');
    }

    public function reset(Request $request)
    {
        if (!$request->isMethod('post')) {

            return response()->json(['error' => true, 'data' => []], 404);
        }

        /** @var User $user */
        $user = User::where('id', $request->id)->first();

        if (!$user) {
            return redirect('/admin');
        }

        $provider = $user->provider;

        if ($provider->id == $request->pid) {
            if ($provider->email == $user->email) {
                $provider->email = '';
            }

            $user->provider_id = null;
            $provider->user_id = null;

            $user->save();
            $provider->save();
        }

        if ($request->backUrl) {
            return redirect($request->backUrl);
        }

        return redirect('/admin');
    }
}
