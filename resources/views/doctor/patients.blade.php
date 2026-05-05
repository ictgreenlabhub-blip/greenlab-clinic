@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

<h3>My Patients</h3>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif



<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @forelse($appointments as $a)
        <tr>

            <!-- NAME (not clickable na) -->
            <td>
                {{ $a->first_name }} {{ $a->last_name }}
            </td>

            <td>{{ $a->appointment_date }}</td>

            <td>
                {{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}
            </td>

            <td>
                <span class="badge 
                    @if($a->status == 'Approved') bg-primary
                    @elseif($a->status == 'In Consultation') bg-warning
                    @elseif($a->status == 'Done') bg-success
                    @else bg-secondary
                    @endif
                ">
                    {{ $a->status }}
                </span>
            </td>

            <!-- ✅ FIX: ONE TD ONLY -->
            <td>
                
                <button class="btn btn-success btn-sm"
                 onclick="openAction(
                    '{{ $a->appointment_id }}',
                    '{{ $a->first_name }} {{ $a->last_name }}',
                    '{{ $a->gender ?? '' }}',
                    '{{ $a->address ?? '' }}',
                    '{{ $a->birthdate ?? '' }}',
                    '{{ $a->appointment_date ?? '' }}'
                )">
                    Action
                </button>
                <button class="btn btn-success btn-sm"
                    onclick="openConsult({{ $a->appointment_id }})">
                    Notes
                </button>

                <button class="btn btn-info btn-sm"
                    onclick="viewMedcert({{ $a->appointment_id }})">
                    Medcert
                </button>
                <button class="btn btn-info btn-sm"
                onclick="viewRx({{ $a->appointment_id }})">
                    View Rx
                <form action="/send-documents/{{ $a->appointment_id }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm ms-1">
                        Send Documents
                    </button>
                </form>


                


            </td>

        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No patients</td>
        </tr>
        @endforelse
    </tbody>
</table>


<!-- ================= CONSULT MODAL ================= -->
<div id="consultModal" class="custom-modal">
    <div class="modal-box">

        <h5>Consult Patient</h5>

        <form id="consultForm" method="POST">
            @csrf

            <textarea name="consultation_notes" class="form-control mb-2" placeholder="Notes"></textarea>

            <input name="diagnosis" class="form-control mb-2" placeholder="Diagnosis">

            <button class="btn btn-success w-100">Save Consultation</button>
        </form>

        <button onclick="closeConsult()" class="btn btn-secondary w-100 mt-2">Close</button>

    </div>
</div>
<!-- VIEW CONSULT MODAL -->
<div id="viewModal" class="custom-modal">
    <div class="modal-box">

        <h5 id="vName"></h5>

        <label><b>Notes:</b></label>
        <p id="vNotes"></p>

        <label><b>Diagnosis:</b></label>
        <p id="vDiagnosis"></p>

        <button onclick="closeView()" 
                class="btn btn-secondary w-100 mt-2">
            Close
        </button>

    </div>
</div>


<!-- ================= ACTION MODAL ================= -->
<div id="actionModal" class="custom-modal">
    <div class="modal-box">

        <h5 id="patientName"></h5>
                
        <!-- MEDCERT -->
        <button class="btn btn-primary w-100 mb-2"
            onclick="openMedCert(selectedID, selectedName)">
            Issue Medical Certificate
        </button>

        <!-- PRESCRIPTION -->
        <button class="btn btn-primary w-100 mb-2"
            onclick="openPrescription(
                selectedID,
                selectedName,
                selectedGender,
                selectedAddress,
                selectedBirthdate,
                selectedDate
            )">
           Issue Prescription
        </button>

        <!-- CLOSE -->
        <button onclick="closeAction()" class="btn btn-secondary" style="width:100%; margin-top:10px;">
            Close
        </button>

    </div>
</div>
 
<!-- ================= MEDCERT MODAL ================= -->
<div id="medcertModal" class="custom-modal">
    <div class="modal-box">

        <h5>Medical Certificate</h5>

        <form method="POST" action="/doctor/medcert/save">
            @csrf

            <input type="hidden" name="appointment_id" id="mc_id">

            <!-- AUTO NAME -->
            <label>Patient Name</label>
            <input class="form-control mb-2" 
                   id="mc_name" 
                   name="patient_name" 
                   readonly>

            <!-- AUTO DATE -->
            <label>Date Issued</label>
            <input class="form-control mb-2" 
                   name="date_issued" 
                   value="{{ date('Y-m-d') }}" 
                   readonly>

            <!-- AUTO DOCTOR -->
            <input type="hidden" name="doctor" value="{{ session('doctor_name') }}">

            <!-- FINDINGS -->
            <label>Findings</label>
            <textarea class="form-control mb-2" 
                      name="findings" 
                      placeholder="Enter findings"></textarea>

            <!-- REMARKS -->
            <label>Remarks</label>
            <textarea class="form-control mb-2" 
                      name="remarks" 
                      placeholder="Enter remarks"></textarea>

            <!-- DISPLAY INFO -->
            <div class="mb-2">
                <small>
                    <b>Doctor:</b> {{ session('doctor_name') }} <br>
                    <b>License:</b> 123456 <br>
                    <b>PTR:</b> 789101
                </small>
            </div>

            <button class="btn btn-success w-100">
                Generate Medical Certificate
            </button>

        </form>

        <button onclick="closeMedCert()" 
                class="btn btn-secondary w-100 mt-2">
            Close
        </button>

    </div>
</div>


<!-- ================= PRESCRIPTION MODAL ================= -->
<div id="rxModal" class="custom-modal">
    <div class="modal-box">

        <h5>Prescription</h5>

        <form method="POST" action="/doctor/rx/save">
            @csrf

            <!-- ID -->
            <input type="hidden" name="appointment_id" id="rx_id">

            <!-- AUTO NAME -->
            <label>Patient Name</label>
            <input class="form-control mb-2" 
                   id="rx_name" 
                   name="patient_name" 
                   readonly>

            <!-- AUTO AGE -->
            <label>Age</label>
            <input class="form-control mb-2" 
                   id="rx_age" 
                   name="age" 
                   readonly>

            <!-- AUTO SEX -->
            <label>Sex</label>
            <input class="form-control mb-2" 
                   id="rx_sex" 
                   name="sex" 
                   readonly>

            <!-- AUTO ADDRESS -->
            <label>Address</label>
            <input class="form-control mb-2" 
                   id="rx_address" 
                   name="address" 
                   readonly>

            <!-- AUTO DATE -->
            <label>Date</label>
            <input class="form-control mb-2" 
                   id="rx_date"
                   name="date"
                   readonly>

            <label>Procedure</label>
                <select name="method" class="form-control mb-2">
                    <option>CONSULTATION</option>
                    <option>X-RAY</option>
                    <option>ULTRASOUND</option>
                    <option>LABORATORY</option>
                    <option>ECG</option>
                </select>

                <label>Prescription</label>
                <textarea name="prescription" class="form-control mb-2"></textarea>

                <label>Referred by</label>
                <textarea name="referred_by" class="form-control mb-2"></textarea>

                <label>Follow up check-up</label>
                <textarea name="follow_up" class="form-control mb-2"></textarea>
            <!-- AUTO DOCTOR -->
            <input type="hidden" name="doctor" value="{{ session('doctor_name') }}">

            <div class="mb-2">
                <small>
                    <b>Doctor:</b> {{ session('doctor_name') }} <br>
                    <b>License:</b> 123456 <br>
                    <b>PTR:</b> 789101
                </small>
            </div>

            <button class="btn btn-primary w-100">
                Generate Prescription
            </button>

        </form>

        <button onclick="closeRx()" 
                class="btn btn-secondary w-100 mt-2">
            Close
        </button>

    </div>
</div>
<div class="modal fade" id="viewRxModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header">
        <h5>Prescription Preview</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <iframe id="rxFrame" style="width:100%; height:80vh;"></iframe>
      </div>

    </div>
  </div>
</div>



<div id="medcertModal" class="custom-modal">
    <div class="modal-content-lg">

        <!-- HEADER -->
        <div class="modal-header">
            <h5>Medical Certificate</h5>

            <div>
                @if(isset($p->appointment_id))
                    <button onclick="openMedcert({{ $p->appointment_id }})">
                @endif

                <button onclick="closeMedcertModal()" class="btn btn-secondary btn-sm">X</button>
            </div>
        </div>

        <!-- BODY -->
        <div id="medcertContent" class="modal-body">
            Loading...
        </div>

    </div>
</div>
<!-- ================= VIEW MEDCERT MODAL ================= -->
<div class="modal fade" id="viewMedcertModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title">Medical Certificate Preview</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">
        <div class="iframe-container">
            <iframe id="medcertFrame"></iframe>
        </div>
      </div>

      <!-- FOOTER -->
      <div class="modal-footer">
        <a onclick="window.location.href='/doctor/download-medcert/64'" class="btn btn-success">
    DOWNLOAD PDF
</a>


        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>


<h4 class="mt-4">Completed Consultations</h4>
<div class="table-container">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Date</th>
                <th>Time</th>
                <th>Diagnosis</th>
                <th>Action</th>
            </tr>
        </thead>

            <tbody>
                @forelse($completed as $a)
                <tr>
                    <td>{{ $a->first_name }} {{ $a->last_name }}</td>
                    <td>{{ $a->appointment_date }}</td>
                    <td>{{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}</td>
                    <td>{{ $a->diagnosis ?? '-' }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary"
                                onclick="viewConsult(
                                    `{{ $a->first_name }} {{ $a->last_name }}`,
                                    `{{ $a->consultation_notes }}`,
                                    `{{ $a->diagnosis }}`
                                )">
                            View
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">
                        No completed consultations
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
</div>
@endsection


<script>

// ================= GLOBAL =================
let selectedID = '';
let selectedName = '';
let selectedGender = '';
let selectedAddress = '';
let selectedBirthdate = '';
let selectedDate = '';

// ================= CONSULT =================
function openConsult(id){
    document.getElementById('consultForm').action = "/doctor/consult/" + id;
    document.getElementById('consultModal').style.display = "flex";
}

function closeConsult(){
    document.getElementById('consultModal').style.display = "none";
}
function viewConsult(name, notes, diagnosis){
    document.getElementById('vName').innerText = name;
    document.getElementById('vNotes').innerText = notes || '-';
    document.getElementById('vDiagnosis').innerText = diagnosis || '-';

    document.getElementById('viewModal').style.display = "flex";
}

function closeView(){
     document.getElementById('viewModal').style.display = "none";
}

// ================= ACTION =================
function openAction(id, name, gender, address, birthdate, date){

    selectedID = id;
    selectedName = name;
    selectedGender = gender;
    selectedAddress = address;
    selectedBirthdate = birthdate;
    selectedDate = date;

    document.getElementById('patientName').innerText = name;

    document.getElementById('actionModal').style.display = "flex";
}



function closeAction(){
    document.getElementById('actionModal').style.display = "none";
}


// ================= MEDCERT =================
function openMedCert(id, name){

    document.getElementById('mc_id').value = id;
    document.getElementById('mc_name').value = name;

    closeAction();
    document.getElementById('medcertModal').style.display = "flex";
}

function closeMedCert(){
    document.getElementById('medcertModal').style.display = "none";
}
// OPEN PRESCRIPTION

function openPrescription(id, name, gender, address, birthdate, date){

    document.getElementById('rx_id').value = id;
    document.getElementById('rx_name').value = name;
    document.getElementById('rx_sex').value = gender;
    document.getElementById('rx_address').value = address;
    document.getElementById('rx_date').value = date;

    // 🔥 AUTO COMPUTE AGE
    if(birthdate){
        let today = new Date();
        let bday = new Date(birthdate);
        let age = today.getFullYear() - bday.getFullYear();
        let m = today.getMonth() - bday.getMonth();

        if (m < 0 || (m === 0 && today.getDate() < bday.getDate())) {
            age--;
        }

        document.getElementById('rx_age').value = age;
    }

    document.getElementById('rxModal').style.display = "flex";
}

function closeRx(){
    document.getElementById('rxModal').style.display = "none";
}



function openMedcertModal(id) {

    document.getElementById('medcertModal').style.display = 'block';

    // LOAD HTML VIEW (NOT PDF)
    fetch('doctor/medcert/view/' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('medcertContent').innerHTML = html;
        });

     // 👇 FIXED DOWNLOAD
    document.getElementById('downloadBtn').onclick = function(e) {
        e.preventDefault();
        window.location.href = '/doctor/download-medcert/' + id;
    };

    document.getElementById('medcertModal').style.display = 'block';
}

function closeMedcertModal() {
    document.getElementById('medcertModal').style.display = 'none';
}
function viewMedcert(id)
{
    // preview URL (HTML view)
    document.getElementById('medcertFrame').src = "/doctor/medcert/view/" + id;

   
    new bootstrap.Modal(document.getElementById('viewMedcertModal')).show();
}

function viewRx(id){
    document.getElementById('rxFrame').src = "/doctor/rx/view/" + id;
    new bootstrap.Modal(document.getElementById('viewRxModal')).show();
}



</script>
