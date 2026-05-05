@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- HEADER -->
    <div class="card p-4 mb-3 shadow-sm" style="background:linear-gradient(90deg,#0aa06e,#18c48f);color:white;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Patient Management</h4>
                <small>{{ count($activePatients ?? []) }} registered patients</small>
            </div>
            <h4>Welcome, {{ auth()->user()->name }}</h4>
                <p>Role: {{ auth()->user()->role }}</p>

            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#patientModal">
                + Register Patient
            </button>
        </div>
    </div>

    <!-- ACTIVE TABLE -->
    <div class="card p-3 shadow-sm mb-4">
        <h5>Active Patients</h5>

        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            @forelse($activePatients ?? [] as $p)
                <tr>
                    <td>
                        <strong>{{ $p->first_name }} {{ $p->last_name }}</strong><br>
                        <small>ID: {{ $p->patient_id }}</small>
                    </td>

                    <td>{{ $p->contact ?? 'N/A' }}</td>
                    <td>{{ $p->address ?? 'N/A' }}</td>

                    <td>
                        <span class="badge bg-success">Active</span>
                    </td>

                    <td>
                        <button class="btn btn-sm btn-primary"
                        onclick="openView('{{ $p->first_name }}','{{ $p->last_name }}','{{ $p->contact }}','{{ $p->address }}','{{ $p->status }}')">
                        👁
                        </button>

                        <button class="btn btn-sm btn-warning"
                        onclick="openEdit({{ $p->patient_id }},'{{ $p->first_name }}','{{ $p->last_name }}','{{ $p->contact }}','{{ $p->address }}','{{ $p->status }}')">
                        ✏️
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No active patients</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- HISTORY -->
    <div class="card p-3 shadow-sm">
        <h5>History</h5>

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
            @forelse($historyPatients ?? [] as $p)
                <tr>
                    <td>{{ $p->first_name }} {{ $p->last_name }}</td>
                    <td><span class="badge bg-secondary">Done</span></td>
                </tr>
            @empty
                <tr><td colspan="2">No history</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>


<!-- ================= REGISTER MODAL ================= -->
<div class="modal fade" id="patientModal">
<div class="modal-dialog modal-xl">
<div class="modal-content">

<div class="modal-header">
    <h5>Register Patient</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal">Personal</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#contact">Contact</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#medical">Medical</button>
    </li>
</ul>

<form method="POST" action="/patients/store">
@csrf

<div class="tab-content">

<!-- PERSONAL -->
<div class="tab-pane fade show active" id="personal">
    <div class="row">
        <div class="col"><input name="first_name" class="form-control mb-2" placeholder="First Name"></div>
        <div class="col"><input name="middle_name" class="form-control mb-2" placeholder="Middle Name"></div>
        <div class="col"><input name="last_name" class="form-control mb-2" placeholder="Last Name"></div>
    </div>

    <div class="row">
        <div class="col"><input type="date" name="birthdate" class="form-control mb-2"></div>
        <div class="col">
            <select name="gender" class="form-control mb-2">
                <option>Male</option>
                <option>Female</option>
            </select>
        </div>
    </div>
</div>

<!-- CONTACT -->
<div class="tab-pane fade" id="contact">
    <input name="contact" class="form-control mb-2" placeholder="Contact">
    <input name="email" class="form-control mb-2" placeholder="Email">
    <input name="address" class="form-control mb-2" placeholder="Address">
</div>

<!-- MEDICAL -->
<div class="tab-pane fade" id="medical">
    <input name="blood_type" class="form-control mb-2" placeholder="Blood Type">
    <input name="philhealth" class="form-control mb-2" placeholder="Philhealth">
    <input name="hmo" class="form-control mb-2" placeholder="HMO">
    <textarea name="notes" class="form-control" placeholder="Notes"></textarea>
</div>

</div>

<button class="btn btn-success w-100 mt-3">Save</button>

</form>

</div>
</div>
</div>
</div>


<!-- ================= VIEW MODAL ================= -->
<div class="modal fade" id="viewModal">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
    <h5>Patient Details</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <p><b>Name:</b> <span id="v_name"></span></p>
    <p><b>Contact:</b> <span id="v_contact"></span></p>
    <p><b>Address:</b> <span id="v_address"></span></p>
    <p><b>Status:</b> <span id="v_status"></span></p>
</div>

</div>
</div>
</div>


<!-- ================= EDIT MODAL ================= -->
<div class="modal fade" id="editModal">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
    <h5>Edit Patient</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<form method="POST" id="editForm">
@csrf

<input id="e_first" name="first_name" class="form-control mb-2">
<input id="e_last" name="last_name" class="form-control mb-2">
<input id="e_contact" name="contact" class="form-control mb-2">
<input id="e_address" name="address" class="form-control mb-2">

<select id="e_status" name="status" class="form-control mb-2">
    <option value="active">Active</option>
    <option value="done">Done</option>
</select>

<button class="btn btn-success w-100">Update</button>

</form>

</div>
</div>
</div>
</div>

</div> <!-- end container -->

<!-- 🔥 DITO MO ILALAGAY -->
<div id="myAppointmentModal" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">

    <div style="background:#fff; padding:20px; width:400px; margin:100px auto; border-radius:10px; text-align:center;">

        <h5>My Appointment</h5>

        <div id="appointmentContent">
            Loading...
        </div>

        <button class="btn btn-secondary w-100 mt-2" onclick="closeMyAppointment()">
            Close
        </button>

    </div>
</div>
</body>
</html>

<!-- JS -->
<script>
function openMyAppointment() {

    fetch('/patient/my-appointment')
    .then(res => res.json())
    .then(data => {

        let html = '';

        if (data) {
            html = `
                <p><b>Name:</b> ${data.first_name} ${data.last_name}</p>
                <p><b>Date:</b> ${data.appointment_date}</p>
                <p><b>Time:</b> ${data.appointment_time}</p>
                <p><b>Doctor:</b> ${data.doctor_name ?? 'Not Assigned'}</p>
                <p><b>Status:</b> ${data.status}</p>
            `;

            if (data.zoom_link) {
                html += `
                    <a href="${data.zoom_link}" target="_blank" 
                       class="btn btn-success w-100">
                       Join Zoom
                    </a>
                `;
            }

        } else {
            html = `<p>No appointment yet.</p>`;
        }

        document.getElementById('appointmentContent').innerHTML = html;
        document.getElementById('myAppointmentModal').style.display = 'block';
    });
}

function closeMyAppointment() {
    document.getElementById('myAppointmentModal').style.display = 'none';
}
</script>


<!-- ================= JS ================= -->
<script>

function openView(f,l,c,a,s){
    document.getElementById('v_name').innerText = f+" "+l;
    document.getElementById('v_contact').innerText = c;
    document.getElementById('v_address').innerText = a;
    document.getElementById('v_status').innerText = s;

    new bootstrap.Modal(document.getElementById('viewModal')).show();
}

function openEdit(id,f,l,c,a,s){
    document.getElementById('editForm').action = "/patients/update/"+id;

    document.getElementById('e_first').value = f;
    document.getElementById('e_last').value = l;
    document.getElementById('e_contact').value = c;
    document.getElementById('e_address').value = a;
    document.getElementById('e_status').value = s;

    new bootstrap.Modal(document.getElementById('editModal')).show();
}

</script>

@endsection