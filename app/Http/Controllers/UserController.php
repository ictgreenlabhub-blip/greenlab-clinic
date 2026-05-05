<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {

        $request->session()->regenerate();

        // 👉 DITO MO ILALAGAY
        return redirect('/dashboard');
    }

    return back()->withErrors([
        'email' => 'Invalid credentials',
    ]);
}
