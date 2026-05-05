<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function save(Request $request)
    {
        DB::table('prescriptions')->insert([
            'appointment_id' => $request->appointment_id,
            'patient_name' => $request->patient_name,
            'age' => $request->age,
            'sex' => $request->sex,
            'address' => $request->address,
            'method' => $request->method,
            'prescription' => $request->prescription,
            'referred_by' => $request->referred_by,
            'follow_up' => $request->follow_up,
            'doctor' => $request->doctor,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Prescription saved!');
    }
    public function view($id)
{
    $rx = DB::table('prescriptions')
        ->where('appointment_id', $id)
        ->first();

    if(!$rx){
        return "No prescription found";
    }

    return view('doctor.prescription_template', compact('rx'));
}
}
