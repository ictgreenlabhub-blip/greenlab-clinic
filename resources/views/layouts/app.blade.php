<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GreenLab Clinic</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

<div class="app">

    {{-- ================= SIDEBAR ================= --}}
    <div class="sidebar">

        <h5 class="text-center mt-3">GreenLab Clinic</h5>

        <ul class="menu">

            <p class="menu-title">Clinic</p>

                            {{-- ADMIN --}}
                @if(auth()->check() && auth()->user()->role == 'admin')
                    <a href="/appointments">Appointments</a>
                    <a href="/registration">Patient Registration</a>
                    <a href="/doctor">Doctor Dashboard</a>
                    <a href="/doctor/patients">Patients</a>
                @endif

                
                {{-- SECRETARY --}}
                @if(auth()->check() && auth()->user()->role == 'secretary')
                    <a href="/appointments">Appointments</a>
                    <a href="/appointments/registration">Patient Registration</a>
                @endif

                {{-- DOCTOR --}}
                @if(auth()->check() && auth()->user()->role == 'doctor')
                    <a href="/doctor">Dashboard</a>
                    <a href="/doctor/patients">Patients</a>
                @endif

                {{-- PATIENT --}}
                @if(auth()->check() && auth()->user()->role == 'patient')

                    <a href="/patient/dashboard">My Dashboard</a>

                    <a href="/my-appointments">My Appointments</a>

                    <a href="/patient/history">History</a>

                @endif   

        </ul>

        <hr>

        {{-- USER DROPDOWN --}}
        <div class="p-3">

            <div class="dropdown">
                <a href="#" class="dropdown-toggle text-white" data-bs-toggle="dropdown">
                    {{ auth()->user()->name ?? 'User' }}
                </a>

                <ul class="dropdown-menu dropdown-menu-dark">

                    <li>
                        <a class="dropdown-item" href="/change-password">
                            Change Password
                        </a>
                    </li>

                    <li>
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                Logout
                            </button>
                        </form>
                    </li>

                </ul>
            </div>

        </div>

    </div>

    {{-- ================= CONTENT ================= --}}
    <div class="content">
        @yield('content')
    </div>

</div>


</body>
</html>