<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class MedcertController extends Controller
{
    public function viewMedcert($id)
{
    $data = DB::table('medical_certificates')
        ->where('appointment_id', $id)
        ->latest()
        ->first();

    if(!$data){
        return "No data found";
    }

    return view('doctor.medcert_template', compact('data'));
}


    public function downloadMedcert($id)
{
    
    $medcert = DB::table('medical_certificates')
        ->where('appointment_id', $id)
        ->first();

    if (!$medcert) {
        return "No medcert found";
    }

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'doctor.medcert_pdf',
        compact('medcert')
    );

    return response()->streamDownload(function () use ($pdf) {
        echo $pdf->output();
    }, 'medical_certificate.pdf');
}
}