<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PatientDashboardController extends Controller
{
    public function index()
    {
        // GET PATIENT (temporary: first record)
        $patient = DB::table('patients')->first();

        if (!$patient) {
            return "NO PATIENT FOUND";
        }

        // UPCOMING (latest scheduled only - single)
        $appointment = DB::table('appointments')
            ->where('patient_id', $patient->patient_id)
            ->where('status', 'Scheduled')
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->first();

        // OPTIONAL: kung gusto mo multiple upcoming
        $appointments = DB::table('appointments')
            ->where('patient_id', $patient->patient_id)
            ->where('status', 'Scheduled')
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        // HISTORY (Done + Cancelled)
        $history = DB::table('appointments')
            ->where('patient_id', $patient->patient_id)
            ->where('status', '!=', 'Scheduled')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        return view('patients_registration.index', compact(
            'patient',
            'appointment',
            'appointments',
            'history'
        ));
    }
}