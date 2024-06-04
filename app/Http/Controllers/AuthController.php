<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                Session::flash('status', 'failed');
                Session::flash('message', 'Your account is not active yet, please contact admin');
                return redirect()->route('login');
            }

            $request->session()->regenerate();
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

    public function registerProcess(Request $request)
    {
        // dd($request->all());
        // Validasi input
        $validated = $request->validate([
            'name_rent' => 'required|max:255',
            'username' => 'required|unique:users|max:255',
            'password' => 'required|max:255',
            'photo_rent' => 'required|file|mimes:jpg,png,jpeg|max:2048', // Atur validasi file sesuai kebutuhan Anda
        ]);

        // dd($validated);
        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        // Proses unggah file
        if ($request->hasFile('photo_rent')) {
            $photoPath = $request->file('photo_rent')->store('photos', 'public');
            $validated['photo_rent'] = $photoPath;
        }

            // Simpan data ke database
        $user = User::create($validated);

        // Menampilkan pesan sukses menggunakan Session::flash
        Session::flash('status', 'success');
        Session::flash('message', 'User registered successfully');

        return redirect()->route('login');
    }
}
