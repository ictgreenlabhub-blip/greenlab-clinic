<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class EmailController extends Controller
{
    public function sendDocuments($id)
{
    // =========================
    // GET PATIENT
    // =========================
    $appointment = DB::table('appointments')
        ->join('patients', 'appointments.patient_id', '=', 'patients.patient_id')
        ->where('appointments.appointment_id', $id)
        ->select('patients.email', 'patients.first_name')
        ->first();

    // =========================
    // GET MEDCERT
    // =========================
    $medcert = DB::table('medical_certificates')
        ->where('appointment_id', $id)
        ->first();

    // =========================
    // GET PRESCRIPTION
    // =========================
    $rx = DB::table('prescriptions')
        ->where('appointment_id', $id)
        ->first();

    // =========================
    // CHECK CONTENT
    // =========================
    if (!$medcert && !$rx) {
        return back()->with('error', 'Walang document');
    }

    // =========================
    // GENERATE PDF
    // =========================
    $pdf = Pdf::loadView('pdf.combined', [
        'medcert' => $medcert,
        'rx' => $rx,
        'patient' => $appointment
    ]);

    // =========================
    // SEND EMAIL
    // =========================
    Mail::send([], [], function ($message) use ($appointment, $pdf) {
        $message->to($appointment->email)
            ->subject('Clinic Documents')
            ->html("
                <h3>Hello {$appointment->first_name}</h3>
                <p>Attached ang iyong medical documents.</p>
            ")
            ->attachData($pdf->output(), 'documents.pdf');
    });

    return back()->with('success', 'Documents sent!');
}
}