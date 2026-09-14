document.addEventListener("DOMContentLoaded", () => {
  // 1. Password Reset Simulation (Preserved logic with Premium Animation)
  const forgotForm = document.getElementById("forgotForm");
  const forgotAlert = document.getElementById("forgotAlert");

  if (forgotForm && forgotAlert) {
    forgotForm.addEventListener("submit", (event) => {
      event.preventDefault();

      const emailInput = document.getElementById("forgotEmail");
      const email = emailInput ? emailInput.value.trim() : "your email";

      const alertMessageSpan = forgotAlert.querySelector(".alert-message");
      if (alertMessageSpan) {
        alertMessageSpan.textContent = `A secure reset link has been dispatched to ${email}.`;
      } else {
        // Fallback
        forgotAlert.textContent = `A secure reset link has been dispatched to ${email}.`;
      }

      // Reveal alert smoothly
      forgotAlert.classList.remove("d-none");
      forgotAlert.style.opacity = "0";
      setTimeout(() => {
        forgotAlert.style.transition = "opacity 0.5s ease";
        forgotAlert.style.opacity = "1";
      }, 10);

      forgotForm.reset();
    });
  }

  // 2. Trigger Entrance Animation if JS is enabled
  setTimeout(() => {
    const card = document.querySelector(".luxury-auth-card");
    if (card) {
      card.classList.add("active");
    }
  }, 100);
});
