<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\MedcertController;
use Illuminate\Http\Request;



/*
|--------------------------------------------------------------------------
| DEFAULT
|--------------------------------------------------------------------------
*/

 Route::middleware(['auth'])->group(function () {

Route::get('/patient/dashboard', function () {
        return view('patients_registration.index');
    })->middleware('role:patient');

Route::get('/doctor', [DoctorController::class,'doctorDashboard'])
        ->middleware('role:doctor');

    
});
  Route::get('/appointments/registration', [AppointmentController::class, 'registration'])
    ->name('appointments.registration')
    ->middleware('role:secretary');

Route::get('/', function () {
    return redirect('/appointments');
});

   // download pdf
Route::get('/doctor/download-medcert/{id}', [MedcertController::class, 'downloadMedcert']);
/*
|--------------------------------------------------------------------------
| PATIENTS
|--------------------------------------------------------------------------
*/
Route::get('/patients', [PatientController::class, 'index']);
Route::post('/patients/store', [PatientController::class, 'store']);
Route::post('/patients/update/{id}', [PatientController::class, 'update']);
Route::get('/patients/delete/{id}', [PatientController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| APPOINTMENTS
|--------------------------------------------------------------------------
*/

// ✅ IMPORTANT: ilagay muna specific routes

Route::post('/appointments/paid/{id}', [AppointmentController::class, 'markPaid']);
Route::get('/appointments/details/{id}', [AppointmentController::class, 'show']);

// ✅ other actions
Route::post('/appointments/store', [AppointmentController::class, 'store']);
Route::post('/appointments/status/{id}', [AppointmentController::class, 'updateStatus']);
Route::get('/appointments/delete/{id}', [AppointmentController::class, 'destroy']);
Route::post('/appointments/cancel/{id}', [AppointmentController::class, 'cancel']);
Route::post('/appointments/reschedule/{id}', [AppointmentController::class, 'reschedule']);
Route::post('/appointments/assign/{id}', [AppointmentController::class, 'assignDoctor']);
Route::post('/appointments/schedule', [AppointmentController::class, 'schedule']);

// ❗ LAST dapat ito (catch-all calendar route)
Route::get('/appointments/{month?}/{year?}', [AppointmentController::class, 'index']);


/*
|--------------------------------------------------------------------------
| PATIENT DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/patient/dashboard', function () {
    return redirect('/patients');
});


/*
|--------------------------------------------------------------------------
| DOCTOR
|--------------------------------------------------------------------------
*/
Route::get('/doctor', [DoctorController::class, 'doctorDashboard']);
Route::get('/doctor/patients', [DoctorController::class, 'doctorPatients']);
Route::post('/doctor/consult/{id}', [DoctorController::class, 'saveConsultation']);


/*
|--------------------------------------------------------------------------
| MEDICAL CERTIFICATE
|--------------------------------------------------------------------------
*/

// save
Route::post('/doctor/medcert/save', [DoctorController::class, 'saveMedCert']);

// view
Route::get('/doctor/medcert/view/{id}', [DoctorController::class, 'viewMedcert']);

Route::get('/patient/medcert/{id}', [DoctorController::class, 'viewMedcert']);
Route::get('/patient/rx/{id}', [PrescriptionController::class, 'view']);


Route::post('/doctor/rx/save', [PrescriptionController::class, 'save']);
Route::get('/doctor/rx/view/{id}', [PrescriptionController::class, 'view']);
Route::post('/doctor/mark-done/{id}', [AppointmentController::class, 'markDone']);








Route::get('/appointments', function () {

    if (!Auth::check()) {
        return redirect('/login');
    }

    return view('appointments.index', [
        'month' => date('n'),
        'year' => date('Y')
    ]);
});
Route::get('/change-password', function () {
    return view('change-password');
});
Route::post('doctor/start-consultation/{id}', [DoctorController::class, 'startConsultation']);
Route::post('/doctor/send-email/{id}', [DoctorController::class, 'sendEmail']);
Route::post('/send-documents/{id}', [EmailController::class, 'sendDocuments']);


Route::middleware(['auth','role:patient'])->group(function () {

    Route::get('/my-appointments', [PatientController::class, 'myAppointments']);

});
Route::middleware(['auth','role:patient'])->group(function () {

    Route::get('/patient/history', [PatientController::class, 'history']);

});



// SHOW AUTH PAGE
Route::get('/auth', function () {
    return view('auth.auth');
});

// LOGIN
Route::post('/login', function (Request $request) {

    if (Auth::attempt($request->only('email', 'password'))) {
        return redirect('/patient/dashboard');
    }

    return back()->with('error', 'Invalid login');
});

// REGISTER
Route::post('/register', function (Request $request) {

    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => 'patient'
    ]);

    Auth::login($user);

    return redirect('/patient/dashboard');
});


Route::post('/logout', [AuthController::class, 'logout']);


