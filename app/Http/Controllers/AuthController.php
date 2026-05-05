<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class AuthController extends Controller
{
    // show login page
    public function showLogin()
    {
        return view('login');
    }

    // process login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

    $role = auth()->user()->role;

    if ($role == 'admin') {
        return redirect('/appointments'); // admin access all
    }

    if ($role == 'doctor') {
        return redirect('/doctor');
    }

    if ($role == 'secretary') {
        return redirect('/appointments');
    }

    if ($role == 'patient') {
        return redirect('/patients');
    }

}

        return back()->with('error', 'Invalid email or password');
    }


public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/auth');
}
    public function showRegister()
    {
    return view('auth.register');
}


public function register(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6'
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'patient' // 🔥 auto patient
    ]);

    return redirect('/login')->with('success', 'Account created!');
}
}