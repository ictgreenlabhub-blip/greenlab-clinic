<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function dashboard()
    {
        $role = auth()->user()->role;

        // 🔥 DOCTOR
        if ($role == 'doctor') {
            return redirect('/doctor');
        }

        // 🔥 SECRETARY
        if ($role == 'secretary') {
            return redirect('/appointments');
        }

        // 🔥 PATIENT
        if ($role == 'patient') {
            return redirect('/patient/dashboard');
        }

        // 🔥 ADMIN (ALL ACCESS)
        if ($role == 'admin') {
            return redirect('/appointments');
        }

        return "No role assigned";
    }
}