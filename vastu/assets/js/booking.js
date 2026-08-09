// document.getElementById("bookLogout").addEventListener("click", function(e) {
//   e.preventDefault();

//   let confirmLogout = confirm("Are you sure you want to logout?");

//   if (confirmLogout) {
//       window.location.href = "assets/api/logout.php";
//   }
// });

const form = document.getElementById("bookingForm");
const openModalBtn = document.getElementById("openConfirmModal");

openModalBtn.addEventListener("click", function () {

    // Trigger HTML5 validation
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Fill confirmation modal
    document.getElementById('confirmName').innerText =
        document.querySelector('[name="name"]').value;

    document.getElementById('confirmEmail').innerText =
        document.querySelector('[name="email"]').value;

    document.getElementById('confirmMobile').innerText =
        document.querySelector('[name="mobile"]').value;

    document.getElementById('confirmDate').innerText =
        document.querySelector('[name="preferred_date"]').value;

    document.getElementById('confirmTime').innerText =
        document.querySelector('[name="preferred_time"]').value;

    document.getElementById('confirmType').innerText =
        document.querySelector('[name="consultation_type"]').value;

    document.getElementById('confirmAddress').innerText =
        document.querySelector('[name="address"]').value;

    const modal = new bootstrap.Modal(document.getElementById('confirmBookingModal'));
    modal.show();
});


document.getElementById("confirmSubmit").addEventListener("click", function () {

  const form = document.getElementById("bookingForm");
  const formData = new FormData(form);

  fetch("assets/api/booking.php", {
      method: "POST",
      body: formData
  })
  .then(response => response.json())
  .then(data => {

      if (data.status) {

          // Close confirmation modal
          bootstrap.Modal.getInstance(document.getElementById("confirmBookingModal")).hide();

          // Show success modal
          const successModal = new bootstrap.Modal(document.getElementById("successModal"));
          successModal.show();

          // Reset form
          form.reset();

      } else {
          alert(data.message);
      }

  })
  .catch(error => {
      console.error(error);
      alert("Something went wrong.");
  });

});
