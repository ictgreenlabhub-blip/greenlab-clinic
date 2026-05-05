<div>

    <h5>
        {{ $appointment->first_name }} {{ $appointment->last_name }}
    </h5>

    <hr>

    <p><b>Date:</b> {{ $appointment->appointment_date }}</p>
    <p><b>Time:</b> {{ $appointment->appointment_time }}</p>
    <p><b>Status:</b> {{ $appointment->status }}</p>

    <hr>

<!-- ASSIGN DOCTOR -->
<form method="POST" action="/appointments/assign/{{ $appointment->appointment_id }}">
    @csrf
    <select name="doctor_name" class="form-control mb-2">
        <option>Dr. Cruz</option>
        <option>Dr. Santos</option>
    </select>
    <button class="btn btn-secondary w-100 mb-2">Assign Doctor</button>
</form>

<!-- CANCEL -->
<form method="POST" action="/appointments/cancel/{{ $appointment->appointment_id }}">
    @csrf
    <button class="btn btn-danger w-100 mb-2">Cancel</button>
</form>

<!-- CLOSE -->
<button class="btn btn-secondary w-100" onclick="closeModal()">Close</button>

    

</div>
