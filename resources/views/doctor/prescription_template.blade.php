<!DOCTYPE html>
<html>
<head>
    <title>Prescription</title>

    <style>
@page {
    size: 5.5in 8.5in;
    margin: 0;
}

body {
    font-family: Arial;
    background: #e9ecef;
    padding: 20px;
}

/* PAPER */
.paper {
    width: 5.5in;
    height: 8.5in;
    background: #fff;
    margin: auto;
    padding: 25px;
    position: relative;
    box-shadow: 0 0 15px rgba(0,0,0,0.25);
}

/* HEADER */
.header {
    text-align: center;
    position: relative;
}

.logo {
    width: 70px;
    position: absolute;
    left: 0;
    top: 0;
}

/* TEXT */
.clinic-name { font-weight: bold; font-size: 14px; }
.clinic-sub { font-size: 11px; }

/* LINES */
.line {
    border-bottom: 1px solid #000;
    display: inline-block;
}

/* 🔥 FIXED WIDTHS */
.line-name { min-width: 180px; }
.line-age { width: 60px; } /* ✔ hindi na mahaba */
.line-sex { width: 60px; } /* ✔ hindi na mahaba */
.line-address { min-width: 220px; }

/* RX */
.rx {
    position: absolute;
    left: 30px;
    top: 230px;
    font-size: 110px;
    color: rgba(0,0,0,0.08); /* mas subtle */
    font-weight: bold;
    z-index: 0; /* 🔥 nasa likod */
}


/* CONTENT ALIGN FIX */
.content {
    margin-left: 25px; /* ✔ para hindi matabunan ng Rx */
    margin-top: 30px;
}

/* SIGNATURE FIX */
.signature {
    position: absolute;
    bottom: 80px; /* ✔ tinaas natin */
    right: 40px;
    text-align: center;
    font-size: 12px;
    z-index: 3;
}

.signature img {
    width: 120px;
   
}

@media print {
    body { background: none; padding: 0; }
    .paper { box-shadow: none; }
}
</style>


<div class="paper">

    <!-- HEADER -->
    <div class="header">
        <img src="{{ isset($isPdf) ? public_path('images/greenlab.png') : asset('images/greenlab.png') }}" class="logo">

        <div class="clinic-name">GREEN LAB CLINIC AND WELLNESS HUB</div>
        <div class="clinic-sub">Owned and Operated by TGM GREEN MEDICAL CORP.</div>
        <div class="clinic-sub">Purok 4, Barangay Apokon, Tagum City</div>
        <div class="clinic-sub">Telephone Number (084) 655 0783</div>
    </div>

    <br><br>

    <!-- PATIENT INFO -->
    <div>
        Patient Name:
        <span class="line line-name">{{ $rx->patient_name }}</span>

        &nbsp;&nbsp;

        Age:
        <span class="line line-age">{{ $rx->age }}</span>

        &nbsp;&nbsp;

        Sex:
        <span class="line line-sex">{{ $rx->sex }}</span>
    </div>

    <br>

    <div>
        Address:
        <span class="line line-address">{{ $rx->address }}</span>

        &nbsp;&nbsp;

        Date:
        <span class="line">
            {{ \Carbon\Carbon::parse($rx->created_at)->format('F d, Y') }}
        </span>
    </div>

    <hr>

    <!-- RX -->
    <div class="rx">Rx</div>

    <!-- CONTENT -->
    <div class="content">

        <p><b>Procedure:</b> {{ $rx->method }}</p>

        <p><b>Prescription:</b></p>
        <p>{{ $rx->prescription }}</p>

        <br>

        <p><b>Referred by:</b></p>
        <p>{{ $rx->referred_by }}</p>

        <br>

        <p><b>Follow up check-up:</b></p>
        <p>{{ $rx->follow_up }}</p>

    </div>

    <!-- SIGNATURE -->
    <div class="signature">

        <!-- OPTIONAL e-sign -->
         <img src="{{ asset('images/signature.png') }}">

        <div><b>Dr. AYA DANIKA A. ALTERADO MD</b></div>
        <div>LIC NO: 0142226</div>
        <div>PTR NO: 2165087</div>
    </div>

</div>

</body>
</html>