<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AppointmentController extends Controller
{
    

    // =========================
    // ASSIGN DOCTOR
    // =========================
    public function assignDoctor(Request $request, $id)
    {
        DB::table('appointments')
            ->where('appointment_id', $id)
            ->update([
                'doctor_name' => $request->doctor_name
            ]);

        return back();
    }


    // =========================
    // DASHBOARD + CALENDAR
    // =========================
    public function index($month = null, $year = null)
    {
        $month = (int) ($month ?? now()->month);
        $year = (int) ($year ?? now()->year);

        if (!checkdate($month, 1, $year)) {
            $month = now()->month;
            $year = now()->year;
        }

        $calendarDate = Carbon::createFromDate($year, $month, 1);

        // ALL APPOINTMENTS FOR CALENDAR
        $appointments = DB::table('appointments')
            ->where('status', 'Scheduled') // ✅ FIX
            ->get();

        $appointmentsByDay = $appointments->groupBy(function ($a) {
            return Carbon::parse($a->appointment_date)->day;
        });

        $today = now()->toDateString();

        // TODAY (exclude Done para hindi doble)
        $todayAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.patient_id')
            ->select(
                'appointments.*',
                'patients.first_name',
                'patients.last_name',
                'patients.gender',
                'patients.birthdate',
                'patients.reason'
            )
            ->whereDate('appointments.appointment_date', date('Y-m-d'))
            ->where('appointments.status', 'Scheduled') // ✅ FIX
            ->get();

            $nowServing = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.patient_id')
            ->select(
                'appointments.*',
                'patients.first_name',
                'patients.last_name'
            )
            ->whereDate('appointments.appointment_date', now()->toDateString())
            ->where('appointments.status', 'In Consultation')
            ->get();


        // IN PROGRESS
        $inProgress = DB::table('appointments')
            ->where('status', 'In Consultation')
            ->orderBy('appointment_time')
            ->get();

        // ACTIVE (Scheduled only)
        $activeAppointments = DB::table('patients')
            ->where('status', 'Paid')
            ->orderBy('created_at', 'desc')
            ->get();

        // HISTORY (DONE + CANCELLED ONLY)
        $historyAppointments = DB::table('appointments')
            ->whereIn('status', ['Done', 'Cancelled'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        // CALENDAR BUILD
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
        $waitingPatients = DB::table('patients as p')
            ->leftJoin('appointments as a', 'p.patient_id', '=', 'a.patient_id')
            ->where('p.status', 'Paid')
            ->where(function($q) {
                $q->whereNull('a.appointment_date')
                ->orWhereNull('a.appointment_time');
            })
            ->select(
                'p.patient_id',
                'p.first_name',
                'p.last_name',
                'p.status',
                'a.appointment_date',
                'a.appointment_time',
                'a.doctor_name'
            )
            ->orderBy('p.created_at', 'desc')
            ->get();


        return view('appointments.index', compact(
            'calendarDate',
            'calendarWeeks',
            'appointmentsByDay',
            'todayAppointments',
            'activeAppointments',
            'historyAppointments',
            'month',
            'year',
            'inProgress',
            'waitingPatients',
            'nowServing'
        ));
    }


    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        if ($request->appointment_date < date('Y-m-d')) {
            return back()->with('error', 'Hindi pwede ang past date!');
        }

        $exists = DB::table('appointments')
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', 'Scheduled')
            ->exists();

        if ($exists) {
            return back()->with('error', 'Slot already taken!');
        }

        DB::table('appointments')->insert([
            'patient_id' => $request->patient_id ?? null,
            'first_name' => $request->first_name ?? '',
            'middle_name' => $request->middle_name ?? '',
            'last_name' => $request->last_name ?? '',
            'suffix' => $request->suffix ?? '',
            'birthdate' => $request->birthdate ?? null,
            'contact' => $request->contact ?? '',
            'email' => $request->email ?? '',
            'address' => $request->address ?? '',
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'service' => $request->service ?? '',
            'reason' => $request->reason ?? '',
            'status' => 'Scheduled',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/appointments');
    }


    // =========================
    // UPDATE STATUS
    // =========================
    public function updateStatus(Request $request, $id)
{
    // update appointment
    DB::table('appointments')
        ->where('appointment_id', $id)
        ->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);

    // 🔥 KUHANIN patient_id
    $appointment = DB::table('appointments')
        ->where('appointment_id', $id)
        ->first();

    if ($appointment) {
        DB::table('patients')
            ->where('patient_id', $appointment->patient_id)
            ->update([
                'status' => $request->status
            ]);
    }

    return back();
}



    // =========================
    // DELETE
    // =========================
    public function destroy($id)
    {
        DB::table('appointments')
            ->where('appointment_id', $id)
            ->delete();

        return back();
    }



    // =========================
    // VIEW MODAL (OPTIONAL)
    // =========================
    public function show($id)
    {
        $appointment = DB::table('appointments')
            ->where('appointment_id', $id)
            ->first();

        return view('appointments.modal_details', compact('appointment'));
    }


    // =========================
    // AJAX DETAILS (FIXED)
    // =========================
    public function details($id)
    {
        $a = DB::table('appointments')->where('appointment_id', $id)->first();

        if (!$a) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json([
            'id' => $a->appointment_id, // 🔥 IMPORTANT
            'name' => $a->first_name . ' ' . $a->last_name,
            'time' => $a->appointment_time,
            'status' => $a->status
        ]);
    }
    public function registration()
    {
    $patients = DB::table('patients')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('appointments.registration', compact('patients'));
    }

    public function markPaid($id)
    {
    DB::table('patients')
        ->where('patient_id', $id)
        ->update(['status' => 'Paid']);

    return back();
    }
    
public function schedule(Request $request)
{
    DB::table('appointments')->insert([
        'patient_id' => $request->patient_id,
        'appointment_date' => \Carbon\Carbon::parse($request->appointment_date)->format('Y-m-d'),
        'appointment_time' => $request->appointment_time,
        'status' => 'Scheduled',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 🔥 ADD THIS
    DB::table('patients')
        ->where('patient_id', $request->patient_id)
        ->update(['status' => 'Scheduled']);

    return redirect('/appointments');
}
    public function markDone($id)
{
    DB::table('appointments')
        ->where('appointment_id', $id)
        ->update([
            'status' => 'Completed'
        ]);

    $appointment = DB::table('appointments')
        ->where('appointment_id', $id)
        ->first();

    if ($appointment) {
        DB::table('patients')
            ->where('patient_id', $appointment->patient_id)
            ->update([
                'status' => 'Completed'
            ]);
    }

    return back();
}

}