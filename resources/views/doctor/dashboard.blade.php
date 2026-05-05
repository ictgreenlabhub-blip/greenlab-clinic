@extends('layouts.app')

@section('content')

<div class="container-fluid"><h3 class="mb-3">Doctor Dashboard</h3>
<h5 class="mb-4">{{ auth()->user()->name }}</h5>

<div class="row">

    <!-- ================= CALENDAR ================= -->
    <div class="col-md-8">
        <div class="card p-3">

            <table class="table table-bordered text-center calendar-table">
                <thead>
                    <tr>
                        <th>Sun</th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($calendarWeeks as $week)
                    <tr>
                        @foreach($week as $day)
                        <td>

                            @if($day)
                                <div class="date-number">{{ $day }}</div>

                                @foreach($appointmentsByDay[$day] ?? [] as $a)

                                    @php
                                        $color = 'primary';

                                        if($a->status == 'Done') $color = 'success';
                                        elseif($a->status == 'Cancelled') $color = 'danger';
                                        elseif($a->status == 'In Consultation') $color = 'warning';
                                    @endphp

                                    <div class="calendar-event bg-{{ $color }}">
                                        {{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}
                                    </div>

                                @endforeach
                            @endif

                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>

    <!-- ================= TODAY ================= -->
    <div class="col-md-4">
        <div class="card p-3">

            <h5>Today</h5>

            @forelse($todayAppointments as $a)

                <div class="border rounded p-2 mb-3">

                    <strong>{{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}</strong><br>

                    👤 {{ $a->first_name }} {{ $a->last_name }}<br>

                    🧬 {{ $a->sex }}<br>

                    🎂 Age: {{ \Carbon\Carbon::parse($a->birthdate)->age }}<br>

                    📝 {{ $a->reason ?? 'N/A' }}<br>

                    <!-- STATUS -->
                    <span class="badge 
                        @if($a->status == 'Done') bg-success
                        @elseif($a->status == 'Cancelled') bg-danger
                        @elseif($a->status == 'In Consultation') bg-warning
                        @else bg-primary
                        @endif
                    ">
                        {{ $a->status }}
                    </span>

                    <!-- 👉 BUTTON TO OPEN MODAL -->
                    <button class="btn btn-primary btn-sm w-100 mt-2"
                        onclick="openModal(
                            '{{ $a->appointment_id }}',
                            '{{ $a->first_name }} {{ $a->last_name }}',
                            '{{ $a->appointment_time }}',
                            '{{ $a->status }}',
                            '{{ $a->zoom_link }}'
                        )">
                        Manage
                    </button>
                    @if($a->zoom_link)
                    <form method="POST" action="/doctor/send-email/{{ $a->appointment_id }}">
                        @csrf
                        <button class="btn btn-success w-100 mt-2">
                            Send Email
                        </button>
                    </form>
                    @endif
                </div>

            @empty
                <p>No schedule today</p>
            @endforelse

        </div>
    </div>

</div>

</div><!-- ================= MODAL ================= --><div id="appointmentModal" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);"><div style="background:#fff; padding:20px; width:400px; margin:100px auto; border-radius:10px;">

    <h5 id="modalName"></h5>

    <p><b>Time:</b> <span id="modalTime"></span></p>
    <p><b>Status:</b> <span id="modalStatus"></span></p>

    <!-- START CONSULTATION -->
    <form id="startForm" method="POST">
        @csrf
        <input type="hidden" name="status" value="In Consultation">
        <button class="btn btn-warning w-100 mb-2">Start Consultation</button>
    </form>

    <!-- 👉 JOIN ZOOM BUTTON -->
    <a id="zoomBtn" href="#" target="_blank" 
       class="btn btn-success w-100 mb-2" style="display:none;">
       Join Zoom
    </a>

    <!-- MARK AS DONE -->
    <form id="doneForm" method="POST">
        @csrf
        <input type="hidden" name="status" value="Completed">
        <button class="btn btn-success w-100 mb-2">Mark as Done</button>
    </form>

    <button class="btn btn-secondary w-100" onclick="closeModal()">Close</button>

</div>

</div><!-- ================= SCRIPT ================= --><script>
function openModal(id, name, time, status, zoomLink = null) {

    document.getElementById('appointmentModal').style.display = 'block';

    document.getElementById('modalName').innerText = name;
    document.getElementById('modalTime').innerText = time;
    document.getElementById('modalStatus').innerText = status;

    document.getElementById('startForm').action = "/doctor/start-consultation/" + id;
    document.getElementById('doneForm').action = "/appointments/status/" + id;

    let zoomBtn = document.getElementById('zoomBtn');

    if (zoomLink && zoomLink !== 'null' && zoomLink !== '') {
        zoomBtn.style.display = 'block';
        zoomBtn.href = zoomLink;
    } else {
        zoomBtn.style.display = 'none';
    }
}

function closeModal() {
    document.getElementById('appointmentModal').style.display = 'none';
}
</script>@endsection