const testimonialUser = JSON.parse(localStorage.getItem("vastuUser") || "null");
const testimonialAuthLink = document.querySelector("[data-nav-auth]");
const testimonialBackToTop = document.getElementById("backToTop");
const testimonialButtons = document.querySelectorAll(".filter-btn");
const testimonialItems = document.querySelectorAll(".testimonial-item");

if (testimonialUser && testimonialAuthLink) {
  testimonialAuthLink.textContent = testimonialUser.firstName;
  testimonialAuthLink.href = "booking.php";
}

window.addEventListener("scroll", () => {
  testimonialBackToTop.classList.toggle("show", window.scrollY > 280);
});

testimonialBackToTop?.addEventListener("click", () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
});

testimonialButtons.forEach((button) => {
  button.addEventListener("click", () => {
    testimonialButtons.forEach((item) => item.classList.remove("active"));
    button.classList.add("active");
    const filter = button.dataset.filter;

    testimonialItems.forEach((item) => {
      const isVisible = filter === "all" || item.dataset.group === filter;
      item.style.display = isVisible ? "block" : "none";
    });
  });
});

document.querySelectorAll(".newsletter-form").forEach((form) => {
  form.addEventListener("submit", (event) => {
    event.preventDefault();
    form.querySelector("button").textContent = "Subscribed";
  });
});

if (window.anime) {
  anime({
    targets: ".hero-shell .eyebrow, .hero-shell h1, .hero-shell p, .filter-btn, .testimonial-card, .metric-card",
    translateY: [26, 0],
    opacity: [0, 1],
    delay: anime.stagger(70),
    duration: 800,
    easing: "easeOutQuad"
  });
}
