<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
@page {
    size: A4;
    margin: 40px 50px;
}

body {
    font-family: Arial, sans-serif;
    font-size: 19px;
    color: #000;
}

/* HEADER */
.header {
    text-align: center;
    position: relative;
}

.logo {
    position: absolute;
    left: 0;
    top: 0;
    width: 90px;
}

.clinic {
    font-weight: bold;
    font-size: 20px;
}

.sub {
    font-size: 15px;
}

/* TITLE */
.title {
    text-align: center;
    margin-top: 15px;
    font-weight: bold;
     font-size: 20px;

}

/* DATE */
.date {
    text-align: right;
    margin-top: 10px;
}

/* CONTENT */
.content {
    margin-top: 30px;
    line-height: 2;
}

/* INLINE LINE (controlled length) */
.line-name {
    display: inline-block;
    border-bottom: 1px solid #000;
    width: 120px;
}

.line-age {
    display: inline-block;
    border-bottom: 1px solid #000;
    width: 50px;
}

.line-date {
    display: inline-block;
    border-bottom: 1px solid #000;
    width: 100px;
}

/* FULL LINES */
.full-line {
    border-bottom: 1px solid #000;
    width: 100%;
    margin-top: 40px;
}

/* SIGNATURE */
.signature {
    position: absolute;
    right: 60px;
    bottom: 120px;
    text-align: center;
    font-size: 16px;
}

.signature img {
    width: 100px;
}

/* FOOTER */
.footer {
    position: absolute;
    bottom: 40px;
    width: 100%;
    text-align: center;
    font-size: 15px;
    font-style: italic;
}
</style>

</head>

<body>

<!-- HEADER -->
<div class="header">
    <img src="{{ public_path('images/greenlab.png') }}" class="logo">

    <div class="clinic">GREEN LAB CLINIC AND WELLNESS HUB</div>
    <div class="sub">Owned and Operated by TGM GREEN MEDICAL CORP</div>
    <div class="sub">Purok 4, Barangay Apokon, Tagum City, Davao del Norte</div>
    <div class="sub">Telephone Number (084) 655 0783</div>
</div>

<!-- TITLE -->
<div class="title">MEDICAL CERTIFICATE</div>

<!-- DATE -->
<div class="date">
    Date: <span class="line-date">{{ $medcert->date ?? '' }}</span>
</div>

<!-- CONTENT -->
<div class="content">

    <p>To Whom It May Concern:</p>

    <p>
        This is to certify that Mr./Ms 
        <span class="line-name">{{ $medcert->patient_name ?? '' }}</span>,
        <span class="line-age">{{ $medcert->age ?? '' }}</span>
        years old, was seen and physically examined on
        <span class="line-date">{{ $medcert->exam_date ?? '' }}</span>
        with the following findings:
    </p>

    <!-- FINDINGS -->
    <div>
        <b>Findings:</b>
        
        <div class="full-line">{{ $medcert->findings ?? '' }}</div>
        <div class="full-line"></div>
        <div class="full-line"></div>
    </div>

    <!-- REMARKS -->
     <b>Remarks:</b>
    
        <div class="full-line">{{ $medcert->remarks ?? '' }}</div>
        <div class="full-line"></div>
        <div class="full-line"></div>
    </div>
    
</div>

<!-- SIGNATURE -->
<div class="signature">
    <img src="{{ public_path('images/signature.png') }}"><br>
    Dr. AYA DANIKA A. ALTERADO MD<br>
    LIC NO: 0142226<br>
    PTR NO: 2165087
</div>

<!-- FOOTER -->
<div class="footer">
    "Where accuracy meets compassion: Your trusted wellness hub"
</div>

</body>
</html>