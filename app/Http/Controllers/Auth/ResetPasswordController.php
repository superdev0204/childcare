<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Models\User;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/'; // Customize the redirect URL after password reset

    public function showResetForm(Request $request)
    {
        return view('passwords.reset');
    }

    public function reset(Request $request)
    {
        $method = $request->method();
        $message = '';

        $resetCode = request()->route()->parameter('rc');
        $uid = request()->route()->parameter('uid');

        if (!$resetCode || !$uid) {
            return redirect('/');
        }

        /** @var User $user */
        $user = User::where('id', $uid)
                    ->where('resetcode', $resetCode)
                    ->first();

        if (!$user) {
            return redirect('/');
        }

        if ($method == "POST") {
            $message = 'Please make the following corrections and submit again.';
            
            $valid_item = [
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6'
            ];
            
            $validated = $request->validate($valid_item);
            
            $user->password = bcrypt($request->password);
            $user->status = 1;
            $user->resetcode = '';

            $user->save();

            return redirect('/user/login');
        }

        return view('passwords.reset', compact('message'));
    }
}
