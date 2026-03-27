<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function Index()
    {
        return view('Auth.login');
    }
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if (auth()->user()->status == 1) {
                return redirect()->intended('dashboard');
            } else {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/')->with('loginError', 'User belum diaktivasi !');
            }
        }
        return back()->with('loginError', 'Login gagal !');
    }
    public function store(Request $request)
    {
        $validateData = $request->validate([
                'nama' => 'required|max:255',
                'username' => ['required', 'min:3', 'max:255', 'unique:user'],
                'password' => 'required|confirmed|min:6',
                'password_confirmation' => 'required'
            ]);
        User::create($validateData);
        return redirect('/')->with('Sukses', 'Registration successful, Please Login');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
