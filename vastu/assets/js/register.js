document.addEventListener("DOMContentLoaded", () => {
  // Password Toggle Functionality (Preserved and Optimized)
  const passwordInput = document.getElementById("password");
  const togglePasswordBtn = document.getElementById("togglePassword");

  if (passwordInput && togglePasswordBtn) {
    const icon = togglePasswordBtn.querySelector("i");

    togglePasswordBtn.addEventListener("click", () => {
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("bi-eye-fill");
        icon.classList.add("bi-eye-slash-fill");
        icon.classList.add("text-gold"); // Add premium color on active
      } else {
        passwordInput.type = "password";
        icon.classList.remove("bi-eye-slash-fill");
        icon.classList.add("bi-eye-fill");
        icon.classList.remove("text-gold");
      }
    });
  }

  // Trigger Entrance Animation if JS is enabled
  setTimeout(() => {
    const card = document.querySelector(".luxury-auth-card-wide");
    if (card) {
      card.classList.add("active");
    }
  }, 100);
});
