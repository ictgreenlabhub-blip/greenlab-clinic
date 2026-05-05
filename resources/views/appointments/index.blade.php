@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Appointments</h3>
        
    </div>
                  
                <div class="row">

    <!-- CALENDAR -->
    <div class="col-md-8">

        <div class="card p-3 mb-3">

            <!-- MONTH NAV -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <a href="/appointments/{{ $month-1 }}/{{ $year }}">⬅</a>

                <h5>
                    {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}
                </h5>

                <a href="/appointments/{{ $month+1 }}/{{ $year }}">➡</a>
            </div>

            <!-- CALENDAR -->
            <table class="table table-bordered text-center calendar-table">
                <tr>
                    <th>Sun</th><th>Mon</th><th>Tue</th>
                    <th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                </tr>

                @foreach($calendarWeeks as $week)
                <tr>
                    @foreach($week as $day)
                        <td onclick="selectDate({{ $day ?? 0 }});
                        event.stopPropagation();">

                            @if($day)
                                <div class="date-number">{{ $day }}</div>

                                @foreach($appointmentsByDay[$day] ?? [] as $a)

                                    <div class="calendar-event"
                                    onclick="event.stopPropagation(); openModal(
                                        '{{ $a->appointment_id }}',
                                        '{{ $a->first_name }} {{ $a->last_name }}',
                                        '{{ $a->appointment_time }}',
                                        '{{ $a->status }}')">
                                        {{ $a->first_name }}<br>
                                        <small>{{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}</small>
                                    </div>
                                @endforeach
                            @endif

                        </td>
                    @endforeach
                </tr>
                @endforeach

            </table>

        </div>
    </div>

    <div class="col-md-4">

    <!-- TODAY -->
    <div class="card p-3 mb-3">
        <h5>Today</h5>

        @forelse($todayAppointments as $a)
            <div class="mb-2 border p-2 rounded">
                <strong>{{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}</strong><br>
                {{ $a->first_name }} {{ $a->last_name }}<br>

                <span class="badge bg-success">
                    {{ $a->status }}
                </span>
            </div>
        @empty
            <p>No today appointments</p>
        @endforelse
    </div>

    <!-- ✅ HIWALAY NA IN PROGRESS -->
    <div class="card mt-3 p-3">
    <h5>Now Serving</h5>

    @forelse($nowServing as $a)
        <div class="mb-3">
            <strong>{{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}</strong><br>
            {{ $a->first_name }} {{ $a->last_name }}<br>

            <span class="badge bg-warning">
                {{ $a->status }}
            </span>
        </div>
    @empty
        <p>No active consultation</p>
    @endforelse
</div>


        
</div> <!-- ✅ END NG ROW (calendar + right side) -->


<div class="card p-3 mb-3">
    <h5>Waiting Patients</h5>

    <!-- SCROLL WRAPPER -->
    <div style="max-height: 300px; overflow-y: auto;">

        <table class="table table-bordered table-striped">
            <thead style="position: sticky; top: 0; background: white;">
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($waitingPatients as $p)
                <tr>
                    <td>{{ $p->first_name }} {{ $p->last_name }}</td>

                    <td>{{ $p->appointment_date ?? '-' }}</td>

                    <td>
                        {{ $p->appointment_time 
                            ? \Carbon\Carbon::parse($p->appointment_time)->format('h:i A') 
                            : '-' 
                        }}
                    </td>

                    <td>{{ $p->doctor_name ?? '-' }}</td>

                    <td>
                        <span class="badge bg-warning">Pending</span>
                    </td>

                    <td>
                        <button class="btn btn-primary btn-sm"
                            onclick="openScheduleModal('{{ $p->patient_id }}')">
                            Set Schedule
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>
</div>



<!-- ================= HISTORY ================= -->
<div class="card p-3 mb-3">
    <h5>Completed</h5>

    <!-- SEARCH (fixed sa taas, hindi kasama sa scroll) -->
    <input type="text" id="historySearch" 
           class="form-control form-control-sm mb-2" 
           placeholder="Search name...">

    <!-- SCROLL WRAPPER -->
    <div style="max-height: 300px; overflow-y: auto;">

        <table class="table table-bordered table-striped">
            <thead style="position: sticky; top: 0; background: white; z-index: 2;">
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody id="historyTable">
                @forelse($historyAppointments as $a)
                <tr>
                    <td>{{ $a->first_name }} {{ $a->last_name }}</td>

                    <td>{{ $a->appointment_date }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}
                    </td>

                    <td>{{ $a->doctor_name ?? '-' }}</td>

                    <td>
                        <span class="badge 
                            @if($a->status == 'Done') bg-success
                            @elseif($a->status == 'Cancelled') bg-danger
                            @else bg-secondary
                            @endif
                        ">
                            {{ $a->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">
                        No history
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

<div id="scheduleModal" class="custom-modal">
    <div class="modal-box">
        <h5>Set Schedule</h5>

        <form method="POST" action="/appointments/schedule">
            @csrf

            <input type="hidden" name="patient_id" id="sched_patient_id">

            <label>Date</label>
            <input type="date" name="appointment_date" class="form-control mb-2" required>

            <label>Time</label>
            <input type="time" name="appointment_time" class="form-control mb-2" required>

            <button class="btn btn-success w-100 confirm-btn">Save</button>
           
        </form>

        <button onclick="closeScheduleModal()" class="btn btn-secondary w-100 mt-2">
            Close
        </button>
    </div>
</div>

<!-- ================= MODAL ================= -->
<div id="appointmentModal" class="custom-modal">
<div class="modal-box">

<h5 id="modalTitle">Add Appointment</h5>

<!-- ================= ADD FORM ================= -->
<form id="addForm" method="POST" action="/appointments/store">
@csrf

<input type="hidden" name="appointment_date" id="date">

<input name="first_name" class="form-control mb-2" placeholder="First Name">
<input name="last_name" class="form-control mb-2" placeholder="Last Name">

<input type="time" name="appointment_time" class="form-control mb-2">

<input name="contact" class="form-control mb-2" placeholder="Contact">

<input name="service" class="form-control mb-2" placeholder="Service">

<input name="reason" class="form-control mb-2" placeholder="Reason">

<button class="btn btn-success w-100">Save</button>

</form>


<!-- ================= VIEW / ACTIONS ================= -->
<div id="viewSection" style="display:none;">

<p><b>Name:</b> <span id="m_name"></span></p>
<p><b>Time:</b> <span id="m_time"></span></p>
<p><b>Status:</b> <span id="m_status"></span></p>

<!-- ASSIGN DOCTOR -->
<form id="assignForm" method="POST">
@csrf
<select name="doctor_name" class="form-control mb-2">
    <option>Dr. Cruz</option>
    <option>Dr. Santos</option>
</select>
<button class="btn btn-secondary btn-sm w-100">Assign Doctor</button>
</form>


<form id="cancelForm" method="POST" style="margin-top:5px;">
@csrf
<input type="hidden" name="status" value="Cancelled">
<button class="btn btn-danger w-100 btn-sm">Cancel</button>
</form>


</div>

<button onclick="closeModal()" class="btn btn-secondary w-100 mt-2">Close</button>

</div>
</div>


<!-- ================= SCRIPT ================= -->
<script>
function openAddModal(){
document.getElementById("appointmentModal").style.display="flex";
}

function closeModal(){
document.getElementById("appointmentModal").style.display="none";
}

function selectDate(day){

    if(!day) return;

    let month = "{{ $month }}";
    let year = "{{ $year }}";

    let fullDate = year + "-" + ("0"+month).slice(-2) + "-" + ("0"+day).slice(-2);

    document.getElementById("date").value = fullDate;

    // 👉 DIRECT OPEN (WAG openModal())
    document.getElementById('appointmentModal').style.display = 'flex';

    document.getElementById('addForm').style.display = 'block';
    document.getElementById('viewSection').style.display = 'none';

    document.getElementById('modalTitle').innerText = "Add Appointment";
}
</script>
<script>
function openModal(id, name, time, status) {

    document.getElementById('appointmentModal').style.display = 'flex';

    // 👉 VIEW MODE
    document.getElementById('addForm').style.display = 'none';
    document.getElementById('viewSection').style.display = 'block';

    document.getElementById('modalTitle').innerText = "Appointment Details";

    // SET DATA
    document.getElementById('m_name').innerText = name;
    document.getElementById('m_time').innerText = time;
    document.getElementById('m_status').innerText = status;

    // 🔥 IMPORTANT (ITO ANG FIX)
    document.getElementById('assignForm').action = "/appointments/assign/" + id;
    document.getElementById('cancelForm').action = "/appointments/status/" + id;
    

    document.getElementById('appointmentModal').style.display = 'block';

    document.getElementById('modalName').innerText = name;

    // 🔥 IMPORTANT: set form action dynamically
    document.getElementById('doneForm').action = '/doctor/mark-done/' + id;
}





</script>

<script>
document.getElementById("historySearch").addEventListener("keyup", function() {

    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#historyTable tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();

        row.style.display = text.includes(value) ? "" : "none";
    });

});
</script>
<script>
function openScheduleModal(id){
    document.getElementById('scheduleModal').style.display = 'flex';
    document.getElementById('sched_patient_id').value = id;
}

function closeScheduleModal(){
    document.getElementById('scheduleModal').style.display = 'none';
}
</script>

@endsection
