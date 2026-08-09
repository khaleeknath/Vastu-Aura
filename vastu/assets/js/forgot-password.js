const forgotForm = document.getElementById("forgotForm");
const forgotAlert = document.getElementById("forgotAlert");
const forgotBackToTop = document.getElementById("backToTop");

forgotForm?.addEventListener("submit", (event) => {
  event.preventDefault();
  const email = document.getElementById("forgotEmail").value.trim();
  forgotAlert.textContent = `A reset link has been simulated for ${email}.`;
  forgotAlert.classList.remove("d-none");
});

if (window.anime) {
  anime({
    targets: ".auth-card",
    translateY: [28, 0],
    opacity: [0, 1],
    duration: 900,
    easing: "easeOutExpo"
  });
}

window.addEventListener("scroll", () => {
  forgotBackToTop.classList.toggle("show", window.scrollY > 160);
});

forgotBackToTop?.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
