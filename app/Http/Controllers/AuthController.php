<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $credentials = [
            $login_type => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'login' => 'required|string|unique:users,email|unique:users,mobile',
            'password' => 'required|string|min:6',
            'birthday_day' => 'required|integer',
            'birthday_month' => 'required|integer',
            'birthday_year' => 'required|integer',
            'gender' => 'required|in:male,female',
        ]);

        $birthday = sprintf('%04d-%02d-%02d', $request->birthday_year, $request->birthday_month, $request->birthday_day);

        $is_email = filter_var($request->login, FILTER_VALIDATE_EMAIL);

        $user = \App\Models\User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $is_email ? $request->login : null,
            'mobile' => !$is_email ? $request->login : null,
            'password' => Hash::make($request->password),
            'birthday' => $birthday,
            'gender' => $request->gender,
        ]);

        Auth::login($user);

            return redirect()->intended(route('home'));
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }




}
