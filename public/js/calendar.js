document.addEventListener("DOMContentLoaded", function () {

    const days = document.querySelectorAll(".calendar-day");

    days.forEach(day => {
        day.addEventListener("click", function () {

            let selectedDate = this.getAttribute("data-date");

            document.getElementById("date").value = selectedDate;

            document.getElementById("appointmentForm").style.display = "block";
        });
    });

});