@extends('layouts.app')

@section('content')

<h4>Change Password</h4>

<form method="POST" action="/change-password">
    @csrf

    <input type="password" name="password" class="form-control mb-2" placeholder="New Password">
    <button class="btn btn-primary">Update</button>

</form>

@endsection