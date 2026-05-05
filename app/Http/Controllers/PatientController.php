<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    // =========================
    // LIST (ACTIVE + HISTORY)
    // =========================
    public function index()
{
    $activePatients = DB::table('patients')
    ->leftJoin('appointments', 'patients.patient_id', '=', 'appointments.patient_id')
    ->where('patients.user_id', auth()->id())
    ->where(function($q){
        $q->whereNull('appointments.status')
          ->orWhereNotIn('appointments.status', ['Done','Completed']);
    })
    ->select(
        'patients.*',
        'appointments.status as appointment_status'
    )
    ->get();

    $historyPatients = DB::table('patients')
    ->join('appointments', 'patients.patient_id', '=', 'appointments.patient_id')
    ->where('patients.user_id', auth()->id())
    ->whereIn('appointments.status', ['Done','Completed'])
    ->select(
        'patients.*',
        'appointments.status as appointment_status'
    )
    ->get();

    return view('patients_registration.index', compact('activePatients', 'historyPatients'));
}

    // STORE (ADD PATIENT)
    // =========================
    public function store(Request $request)
    {
            DB::table('patients')->insert([
        'user_id' => auth()->id(), // ✅ IMPORTANT
        'status' => 'active',
        'first_name' => $request->first_name ?? '',
        'middle_name' => $request->middle_name ?? '',
        'last_name' => $request->last_name ?? '',
        'suffix' => $request->suffix ?? '',
        'birthdate' => $request->birthdate ?? null,
        'gender' => $request->gender ?? '',
        'contact' => $request->contact ?? '',
        'email' => $request->email ?? '',
        'address' => $request->address ?? '',
        'created_at' => now(),
        'updated_at' => now(),
    ]);


        return redirect('/patients');
    }


    // =========================
    // UPDATE (EDIT PATIENT)
    // =========================
    public function update(Request $request, $id)
    {
        DB::table('patients')->where('patient_id', $id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'contact' => $request->contact,
            'address' => $request->address,
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return redirect('/patients');
    }


    // =========================
    // DELETE
    // =========================
    public function destroy($id)
    {
        DB::table('patients')->where('patient_id', $id)->delete();

        return redirect('/patients');
    }


    // =========================
    // REGISTRATION PAGE
    // =========================
    public function registration()
    {
        $patients = DB::table('patients')->get();

        return view('appointments.registration', compact('patients'));
    }
    // =========================
// MY APPOINTMENT (MODAL DATA)
// =========================
public function myAppointments()
{
    $appointment = DB::table('appointments')
        ->join('patients', 'appointments.patient_id', '=', 'patients.patient_id')
        ->where('patients.user_id', auth()->id()) // 🔥 FILTER
        ->whereIn('appointments.status', ['Scheduled', 'In Consultation'])
        ->select(
            'appointments.*',
            'patients.first_name',
            'patients.last_name'
        )
        ->orderBy('appointment_date', 'desc')
        ->first(); // 🔥 ISA LANG

    return view('patients_registration.my_appointments', compact('appointment'));
    }


    // =========================
// PATIENT HISTORY
// =========================
    public function history()
{
    $history = DB::table('appointments')
        ->join('patients', 'appointments.patient_id', '=', 'patients.patient_id')
        ->where('patients.user_id', auth()->id())
        ->whereIn('appointments.status', ['Done', 'Completed'])
        ->select(
            'appointments.appointment_id',
            'appointments.appointment_date',
            'appointments.appointment_time',
            'appointments.status',
            'patients.first_name',
            'patients.last_name'
        )
        ->orderBy('appointment_date', 'desc')
        ->get();

    return view('patients_registration.history', compact('history'));
}
}
