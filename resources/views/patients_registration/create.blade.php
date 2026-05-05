<!DOCTYPE html>
<html>
<head>
    <title>Add Patient</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
</head>

<body>

<div class="container mt-5" style="max-width: 500px;">

    <div class="card p-4 shadow">
        <h3 class="mb-4 text-center">Patient Form</h3>

        <form method="POST" action="/patients/store">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-3">
                <input type="text" name="name" class="form-control" placeholder="Full Name" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <input type="number" name="age" class="form-control" placeholder="Age" value="{{ old('age') }}" required>
            </div>

            <div class="mb-3">
                <input type="text" name="gender" class="form-control" placeholder="Gender" value="{{ old('gender') }}" required>
            </div>

            <div class="mb-3">
                <input type="text" name="contact" class="form-control" placeholder="Contact Number" value="{{ old('contact') }}">
            </div>

            <div class="mb-3">
                <input type="text" name="address" class="form-control" placeholder="Address" value="{{ old('address') }}" required>
            </div>

            <button class="btn btn-success w-100">Submit</button>
        </form>
    </div>

</div>

</body>
</html>
