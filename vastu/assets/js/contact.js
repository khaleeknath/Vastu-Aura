const contactUser = JSON.parse(localStorage.getItem("vastuUser") || "null");
const contactAuthLink = document.querySelector("[data-nav-auth]");
const contactBackToTop = document.getElementById("backToTop");
const contactForm = document.getElementById("contactForm");
const contactAlert = document.getElementById("contactAlert");

if (contactUser && contactAuthLink) {
  contactAuthLink.textContent = contactUser.firstName;
  contactAuthLink.href = "booking.php";
}

window.addEventListener("scroll", () => {
  contactBackToTop.classList.toggle("show", window.scrollY > 280);
});

contactBackToTop?.addEventListener("click", () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
});

contactForm?.addEventListener("submit", (event) => {
  event.preventDefault();
  contactAlert.textContent = "Your enquiry has been received. A Vastu advisor will reach out within 24 hours.";
  contactAlert.classList.remove("d-none");
  contactForm.reset();
});

document.querySelectorAll(".newsletter-form").forEach((form) => {
  form.addEventListener("submit", (event) => {
    event.preventDefault();
    form.querySelector("button").textContent = "Subscribed";
  });
});

if (window.anime) {
  anime({
    targets: ".hero-shell .eyebrow, .hero-shell h1, .hero-shell p, .contact-card, .info-card",
    translateY: [30, 0],
    opacity: [0, 1],
    delay: anime.stagger(90),
    duration: 850,
    easing: "easeOutQuad"
  });
}
