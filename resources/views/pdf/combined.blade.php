<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clinic Documents</title>
    <style>
        body { font-family: Arial; font-size: 12px; }
        h2 { margin-bottom: 5px; }
        .section { margin-bottom: 30px; }
        .line { border-bottom: 1px solid #000; margin: 10px 0; }
    </style>
</head>
<body>

{{-- MEDCERT --}}
@if($medcert)
    @include('doctor.medcert_pdf', ['data' => $medcert])
    <div class="page-break"></div>
@endif

{{-- PRESCRIPTION --}}
@if($rx)
    @include('doctor.prescription_template', ['rx' => $rx])
@endif

</body>
</html>