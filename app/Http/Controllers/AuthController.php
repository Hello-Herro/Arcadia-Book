<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class AuthController extends Controller
{
    public function login()
    {
        return view('Login');
    }

    public function register()
    {
        return view('SignUp');
    }

    public function authenticating(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Check if login is valid
        if (Auth::attempt($credentials)) {

            // Check if user status is active
            if (Auth::user()->status != 'active') {
                Session::flash('status', 'failed');
                Session::flash('message', 'Your account is not active yet, please contact admin');
                return redirect()->route('login');
            }

            // $request->session()->regenerate();
            if (Auth::user()->role_id == 1) {
                return redirect()->route('dashboard');
            }
            if (Auth::user()->role_id == 2) {
                return redirect()->route('home');
            }
        }

        Session::flash('status', 'failed');
        Session::flash('message', 'Login invalid');
        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
