@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-center align-items-center" style="height:80vh;">

    <div class="card shadow p-4" style="width:400px; border-radius:12px;">

        <h4 class="text-center mb-3">My Appointment</h4>

        @if($appointment)

            <p><b>Name:</b> {{ $appointment->first_name }} {{ $appointment->last_name }}</p>
            <p><b>Date:</b> {{ $appointment->appointment_date }}</p>
            <p><b>Time:</b> {{ $appointment->appointment_time }}</p>

            <p><b>Doctor:</b> 
                {{ $appointment->doctor_name ?? 'Not Assigned' }}
            </p>

            <p><b>Status:</b> 
                <span class="badge bg-success">{{ $appointment->status }}</span>
            </p>

            @if($appointment->zoom_link)
                <a href="{{ $appointment->zoom_link }}" 
                   target="_blank" 
                   class="btn btn-success w-100">
                   Join Zoom
                </a>
            @else
                <p class="text-muted text-center">No meeting link yet</p>
            @endif

        @else

            <p class="text-center text-muted">No appointment found.</p>

        @endif

        <a href="/patient/dashboard" class="btn btn-secondary w-100 mt-3">
            Back
        </a>

    </div>

</div>

@endsection