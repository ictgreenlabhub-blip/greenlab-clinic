<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;


class DoctorController extends Controller
{
    public function doctorDashboard(Request $request)
{
    $doctor = $request->doctor ?? auth()->user()->name;

    $month = now()->month;
    $year = now()->year;

    // 🔥 CALENDAR (exclude completed)
    $appointments = DB::table('appointments')
        ->whereNotIn('status', ['Scheduled', 'In Consultation', 'Cancelled', 'Done', 'Completed'])
        ->get();

    $appointmentsByDay = $appointments->groupBy(function ($a) {
        return Carbon::parse($a->appointment_date)->day;
    });

    // 🔥 TODAY (FIXED)
    $todayAppointments = DB::table('appointments')
        ->join('patients', 'appointments.patient_id', '=', 'patients.patient_id')
        ->select(
            'appointments.*',
            'patients.first_name',
            'patients.last_name',
            'patients.gender',
            'patients.birthdate',
            'patients.address'
        )
        ->whereRaw("DATE(appointments.appointment_date) = CURDATE()")
        ->whereIn('appointments.status', ['Scheduled', 'In Consultation']) // 🔥 FIX capital
        ->get();

    // 🔥 CALENDAR BUILD (OK NA ITO)
    $calendarDate = Carbon::createFromDate($year, $month, 1);
    $calendarWeeks = [];
    $daysInMonth = $calendarDate->daysInMonth;
    $firstDayOfWeek = $calendarDate->dayOfWeek;

    $currentDay = 1 - $firstDayOfWeek;

    while ($currentDay <= $daysInMonth) {
        $week = [];
        for ($i = 0; $i < 7; $i++) {
            $week[] = ($currentDay >= 1 && $currentDay <= $daysInMonth) ? $currentDay : null;
            $currentDay++;
        }
        $calendarWeeks[] = $week;
    }

    
return view('doctor.dashboard', [
    'doctor' => $doctor,
    'calendarWeeks' => $calendarWeeks,
    'appointmentsByDay' => $appointmentsByDay,
    'todayAppointments' => $todayAppointments,
    'month' => $month,
    'year' => $year,
]);
}

    // ============================
    // PATIENTS PAGE
    // ============================
    public function doctorpatients()
    {
        // 🔥 ACTIVE (Today only)
        $appointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.patient_id')
            ->select(
                'appointments.appointment_id',
                'appointments.appointment_date',
                'appointments.appointment_time',
                'appointments.status',
                'patients.first_name',
                'patients.last_name',
                'patients.gender',
                'patients.birthdate',
                'patients.address'
            )
            ->whereDate('appointments.appointment_date', now())
            ->whereIn('appointments.status', ['Scheduled', 'In Consultation']) // ✅ FIX
            ->get();

        // 🔥 COMPLETED
        $completed = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.patient_id')
            ->select(
                'appointments.*',
                'patients.first_name',
                'patients.last_name'
            )
            ->where('appointments.status', 'Completed')
            ->get();

        return view('doctor.patients', compact('appointments', 'completed'));
    }

    // ============================
    // SAVE CONSULTATION
    // ============================
    public function saveConsultation(Request $request, $id)
    {
        DB::table('appointments')
            ->where('appointment_id', $id)
            ->update([
                'consultation_notes' => $request->consultation_notes,
                'diagnosis' => $request->diagnosis,
                'status' => 'Completed', // ✅ FIX (hindi na Done)
                'updated_at' => now()
            ]);

        return back()->with('success', 'Consultation saved');
    }

    // ============================
    // MEDICAL CERTIFICATE
    // ============================
    public function generateMedCert(Request $request)
    {
        DB::table('medical_certificates')->insert([
            'appointment_id' => $request->appointment_id,
            'patient_name' => $request->patient_name,
            'date_issued' => now(),
            'findings' => $request->findings,
            'remarks' => $request->remarks,
            'doctor_name' => auth()->user()->name ?? 'Dr. Cruz',
            'license' => auth()->user()->license ?? 'N/A',
            'ptr' => auth()->user()->ptr ?? 'N/A',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Medical Certificate Saved!');
    }

    public function viewMedcert($id)
    {
        $data = DB::table('medical_certificates')
            ->where('appointment_id', $id)
            ->latest()
            ->first();

        if (!$data) {
            return "No data found";
        }

        return view('doctor.medcert_template', compact('data'));
    }

    public function downloadMedcert($id)
    {
        $data = DB::table('medical_certificates')
            ->where('id', $id)
            ->first();

        if (!$data) {
            return "No data found";
        }

        $pdf = Pdf::loadView('doctor.medcert_template', compact('data'));

        return $pdf->download('medical_certificate.pdf');
    }

    public function saveMedCert(Request $request)
    {
        DB::table('medical_certificates')->insert([
            'appointment_id' => $request->appointment_id,
            'patient_name' => $request->patient_name,
            'date_issued' => now(),
            'findings' => $request->findings,
            'remarks' => $request->remarks,
            'doctor_name' => auth()->user()->name ?? 'Dr. Cruz',
            'license' => auth()->user()->license ?? '------',
            'ptr' => auth()->user()->ptr ?? '------',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Medical Certificate Saved!');
    }
    public function startConsultation($id)
{
    // ================= GET ZOOM TOKEN =================
    $clientId = env('ZOOM_CLIENT_ID');
    $clientSecret = env('ZOOM_CLIENT_SECRET');
    $accountId = env('ZOOM_ACCOUNT_ID');

    $credentials = base64_encode($clientId . ':' . $clientSecret);

    $response = Http::withHeaders([
        'Authorization' => 'Basic ' . $credentials,
    ])->asForm()->post('https://zoom.us/oauth/token', [
        'grant_type' => 'account_credentials',
        'account_id' => $accountId,
    ]);

    $accessToken = $response->json()['access_token'];

    // ================= CREATE MEETING =================
    $meeting = Http::withToken($accessToken)->post('https://api.zoom.us/v2/users/me/meetings', [
        'topic' => 'Clinic Consultation',
        'type' => 1, // instant meeting
        'settings' => [
            'join_before_host' => true,
        ],
    ]);

    $data = $meeting->json();

    // ================= SAVE TO DB =================
    DB::table('appointments')
        ->where('appointment_id', $id)
        ->update([
            'zoom_link' => $data['join_url'],
            'status' => 'In Consultation'
        ]);

    return redirect()->back()->with('success', 'Consultation started');
}

private function generateZoomMeeting()
{
    $clientId = env('ZOOM_CLIENT_ID');
    $clientSecret = env('ZOOM_CLIENT_SECRET');
    $accountId = env('ZOOM_ACCOUNT_ID');

    $credentials = base64_encode($clientId . ':' . $clientSecret);

    // TOKEN
    $response = Http::withHeaders([
        'Authorization' => 'Basic ' . $credentials,
    ])->asForm()->post('https://zoom.us/oauth/token', [
        'grant_type' => 'account_credentials',
        'account_id' => $accountId,
    ]);

    $accessToken = $response->json()['access_token'];

    // 🔥 FIXED MEETING
    $meeting = Http::withToken($accessToken)
        ->post('https://api.zoom.us/v2/users/me/meetings', [
            'topic' => 'Clinic Consultation',
            'type' => 1,
            'start_time' => now()->addMinutes(2)->format('Y-m-d\TH:i:s'),
            'timezone' => 'Asia/Manila',
            'duration' => 30,
            'password' => '123456',
            'settings' => [
                'join_before_host' => true,
                'waiting_room' => false,
            ],
        ]);

    $data = $meeting->json();

if (!isset($data['join_url'])) {
    dd($data); // makita mo kung ano mali
}

return $data['join_url'];
}

public function sendEmail($id)
{
    $data = DB::table('appointments')
        ->join('patients', 'appointments.patient_id', '=', 'patients.patient_id')
        ->where('appointments.appointment_id', $id)
        ->select(
            'appointments.*',
            'patients.email',
            'patients.first_name',
            'patients.last_name'
        )
        ->first();

    if (!$data || !$data->email) {
        return back()->with('error', 'No email found for this patient');
    }

    $email = $data->email;
    $name = $data->first_name . ' ' . $data->last_name;

    Mail::raw("Hello $name,

Your consultation has been scheduled.

📅 Date/Time: {$data->appointment_time}

🔗 Zoom Link:
{$data->zoom_link}

Please join 5 minutes before your schedule.

Thank you!
Greenlab Clinic
", function ($message) use ($email) {
        $message->to($email)
                ->subject('Consultation Schedule - Greenlab Clinic');
    });

    return back()->with('success', 'Email sent successfully!');
}



}