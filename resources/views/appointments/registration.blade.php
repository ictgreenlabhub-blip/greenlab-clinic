@extends('layouts.app')

@section('content')

<div class="container-fluid"><h4>Patient Registration (Secretary)</h4>

<!-- 🔥 SCROLLABLE TABLE WRAPPER -->
<div class="table-container">

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        @foreach($patients as $p)
            <tr>
                <td>{{ $p->first_name }} {{ $p->last_name }}</td>
                <td>{{ $p->contact }}</td>

                <td>
                    <span class="badge 
                        {{ $p->status == 'Paid' ? 'bg-success' : 'bg-warning' }}">
                        {{ $p->status ?? 'Pending' }}
                    </span>
                </td>

                <td>
                    @if($p->status != 'Paid')
                    <form method="POST" action="/appointments/paid/{{ $p->patient_id }}">
                        @csrf
                        <button class="btn btn-success btn-sm w-100">
                            Mark as Paid
                        </button>
                    </form>
                    @else
                        <span class="text-success">✔ Paid</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>

</div>@endsection