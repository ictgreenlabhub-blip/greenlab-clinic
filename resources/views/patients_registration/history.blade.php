@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <h4 class="mb-4">My History</h4>

    @forelse($history as $h)

        <div class="card mb-3 shadow-sm p-3">

            <div class="row align-items-center">

                {{-- NAME --}}
                <div class="col-md-3">
                    <strong>{{ $h->first_name }} {{ $h->last_name }}</strong>
                </div>

                {{-- DATE --}}
                <div class="col-md-2">
                    {{ $h->appointment_date }}
                </div>

                {{-- TIME --}}
                <div class="col-md-2">
                    {{ $h->appointment_time }}
                </div>

                {{-- STATUS --}}
                <div class="col-md-2">
                    <span class="badge bg-secondary">
                        {{ $h->status }}
                    </span>
                </div>

                {{-- ACTION --}}
                <div class="col-md-3 text-end">

                    <button class="btn btn-success btn-sm"
                        onclick="openViewer('/patient/medcert/{{ $h->appointment_id }}')">
                        MedCert
                    </button>

                    <button class="btn btn-info btn-sm"
                        onclick="openViewer('/patient/rx/{{ $h->appointment_id }}')">
                        RX
                    </button>

                </div>

            </div>

        </div>

    @empty

        <p class="text-muted">No history found.</p>

    @endforelse

</div>


{{-- ================= MODAL VIEWER ================= --}}
<div id="viewerModal" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:999;">

    <div style="background:#fff; width:80%; height:90%; margin:3% auto; border-radius:10px; padding:10px;">

        {{-- iframe --}}
        <iframe id="viewerFrame" 
                style="width:100%; height:90%; border:none;">
        </iframe>

        {{-- close --}}
        <button class="btn btn-danger w-100 mt-2" onclick="closeViewer()">
            Close
        </button>

    </div>
</div>


{{-- ================= JAVASCRIPT ================= --}}
<script>

function openViewer(url) {
    document.getElementById('viewerFrame').src = url;
    document.getElementById('viewerModal').style.display = 'block';
}

function closeViewer() {
    document.getElementById('viewerModal').style.display = 'none';
    document.getElementById('viewerFrame').src = '';
}

// optional: close pag click outside
window.onclick = function(event) {
    let modal = document.getElementById('viewerModal');
    if (event.target == modal) {
        closeViewer();
    }
}

</script>

@endsection