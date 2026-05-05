<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

public function login(Request $request)
{
    if (Auth::attempt($request->only('email', 'password'))) {

        $user = Auth::user();

        if ($user->role == 'doctor') {
            return redirect('/doctor');
        }

        if ($user->role == 'secretary') {
            return redirect('/secretary');
        }

        return redirect('/patient/dashboard'); // default patient
    }

    return back()->with('error', 'Invalid login');
}
