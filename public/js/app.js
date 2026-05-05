function selectDate(day) {

    let fullDate = "2026-04-" + String(day).padStart(2, '0');

    document.getElementById("date").value = fullDate;

    document.getElementById("appointmentForm").style.display = "block";

    alert("Selected date: " + fullDate);
}