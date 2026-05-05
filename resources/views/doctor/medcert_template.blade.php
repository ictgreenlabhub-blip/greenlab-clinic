<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
@page {
    size: A4;
    margin: 60px 70px;
}

body {
    font-family: "Times New Roman", serif;
    font-size: 16px;
    color: #000;
    margin: 60px auto;
    max-width: 794px;
    padding: 40px 60px
}

/* ===== HEADER ===== */
.header {
    text-align: center;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center; /* center buong block */
    position: relative;
}



.clinic-name {
    font-size: 22px;
    font-weight: bold;
    letter-spacing: 1px;
}

.clinic-sub {
    font-size: 13px;
}

/* ===== TITLE ===== */
.title {
    text-align: center;
    font-weight: bold;
    font-size: 22px;
    margin-top: 30px;
    text-decoration: underline;
}

/* ===== DATE ===== */
.date {
    text-align: right;
    margin-top: 30px;
}

/* ===== CONTENT ===== */
.content {
    margin-top: 50px;
    line-height: 2;
}

/* ===== LINES ===== */
.line {
    border-bottom: 1px solid #000;
    display: inline-block;
    padding: 0 5px;
    height: 20px;
    vertical-align: buttom;
    margin: 0 5px;
}

.name-line { width: 220px; }
.age-line { width: 70px; }
.date-line { width: 130px; }

/* FULL LINES */
.line-full {
    border-bottom: 1px solid #000;
    width: 100%;
    margin-top: 10px;
    height: 18px;
}



/* ===== SIGNATURE ===== */
.signature {
    margin-top: 80px;
    text-align: right;
}

.signature img {
    width: 120px;
    margin-bottom: -15px;
}

.doctor-name {
    font-weight: bold;
}

/* ===== FOOTER ===== */
.footer {
    position: fixed;
    bottom: 50px;
    width: 85%;
    text-align: center;
    font-size: 14px;
    font-style: italic;
}
.text-with-line {
    display: inline-block;
    border-bottom: 1px solid #000;
    min-width: 100%; /* para may haba kahit maiksi text */
    padding-bottom: 2px;
    margin-top: 18px;
    height: 22px;
}

/* LOGO */
.logo {
    margin-right: 15px; /* 🔥 space sa text */
}
.header {
    position: relative;
    text-align: center; /* ✔ center text */
    margin-bottom: 20px;
}

/* LOGO SA RIGHT SIDE */
.logo {
    position: absolute;
    right: 0; /* 👉 nasa kanan */
    top: 0;

    
}

/* CENTER TEXT */
.header-center {
    display: inline-block;
}

.clinic-name {
    font-weight: bold;
    font-size: 18px;
}

.clinic-sub {
    font-size: 12px;
}


</style>

</head>

<body>

<!-- HEADER -->
<div class="header">

    <!-- 🔥 IMPORTANT: PUBLIC_PATH para gumana sa PDF -->
    <img src="{{ isset($isPdf) ? public_path('images/greenlab.png') : asset('images/greenlab.png') }}"
    style="width:80px; height:auto; object-fit:contain;">


    <div class="header-center">
    <div class="clinic-name">GREEN LAB CLINIC AND WELLNESS HUB</div>
    <div class="clinic-sub">Owned and Operated by TGM GREEN MEDICAL CORP.</div>
    <div class="clinic-sub">Purok 4, Barangay Apokon, Tagum City, Davao del Norte</div>
    <div class="clinic-sub">Telephone Number (084) 655 0783</div>
    </div>
</div>

<!-- TITLE -->
<div class="title">MEDICAL CERTIFICATE</div>

<!-- DATE -->
<!-- DATE -->
<div class="date">
    Date: <span class="line-date">{{ $medcert->date ?? '' }}</span>
</div>

<!-- CONTENT -->
<div class="content">

<p>To Whom It May Concern:</p>

<p>
    This is to certify that Mr./Ms.

    <span class="line name-line">
        {{ $data->patient_name ?? '' }}
    </span>,

    <span class="line age-line">
        {{ $data->age ?? '' }}
    </span> years old,

    was seen and physically examined on

    <span class="line date-line">
        {{ $data->date_issued ?? '' }}
    </span>

    with the following:
</p>

<b>Findings:</b>

<div class="text-with-line">{{ $data->findings ?? '' }}</div>
<div class="text-with-line"></div>
<div class="text-with-line"></div>


<b>Remarks:</b>

<div class="text-with-line">{{ $data->remarks ?? '' }}</div>
<div class="text-with-line"></div>
<div class="text-with-line"></div>


</div>

<!-- SIGNATURE -->
<div class="signature">

    <!-- 🔥 IMPORTANT: PUBLIC_PATH -->
    <img src="{{ isset($isPdf) ? public_path('images/signature.png') : asset('images/signature.png') }}">

    <div class="doctor-name">{{ $data->doctor_name ?? '' }}</div>
    <div>LIC NO: {{ $data->license ?? '------' }}</div>
    <div>PTR NO: {{ $data->ptr ?? '------' }}</div>

</div>

<!-- FOOTER -->
<div class="footer">
    "Where accuracy meets compassion: Your trusted wellness hub"
</div>

</body>
</html>