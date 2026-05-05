@extends('layouts.app')

@section('content')

<div class="card">
    <h4>User Role Management</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Save</th>
            </tr>
        </thead>

        <tbody>
        @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>

                <td>
                    <form method="POST" action="/settings/users/{{ $u->id }}">
                        @csrf
                        <select name="role" class="form-control">
                            <option value="admin" {{ $u->role=='admin'?'selected':'' }}>Admin</option>
                            <option value="doctor" {{ $u->role=='doctor'?'selected':'' }}>Doctor</option>
                            <option value="secretary" {{ $u->role=='secretary'?'selected':'' }}>Secretary</option>
                            <option value="patient" {{ $u->role=='patient'?'selected':'' }}>Patient</option>
                        </select>
                </td>

                <td>
                        <button class="btn btn-success btn-sm">Save</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection