const aboutUser = JSON.parse(localStorage.getItem("vastuUser") || "null");
const aboutAuthLink = document.querySelector("[data-nav-auth]");
const aboutBackToTop = document.getElementById("backToTop");

if (aboutUser && aboutAuthLink) {
  aboutAuthLink.textContent = aboutUser.firstName;
  aboutAuthLink.href = "booking.php";
}

window.addEventListener("scroll", () => {
  aboutBackToTop.classList.toggle("show", window.scrollY > 280);
});

aboutBackToTop?.addEventListener("click", () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
});

document.querySelectorAll(".newsletter-form").forEach((form) => {
  form.addEventListener("submit", (event) => {
    event.preventDefault();
    form.querySelector("button").textContent = "Subscribed";
  });
});

if (window.anime) {
  anime({
    targets: ".hero-shell .eyebrow, .hero-shell h1, .hero-shell p, .portrait-panel, .story-card, .timeline-item, .value-card",
    translateY: [28, 0],
    opacity: [0, 1],
    delay: anime.stagger(80),
    duration: 850,
    easing: "easeOutQuad"
  });
}
