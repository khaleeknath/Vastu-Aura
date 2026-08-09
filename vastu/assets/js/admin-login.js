const adminLoginForm = document.getElementById("adminLoginForm");
const adminLoginAlert = document.getElementById("adminLoginAlert");
const adminLoginBackToTop = document.getElementById("backToTop");

adminLoginForm?.addEventListener("submit", (event) => {


  
});



window.addEventListener("scroll", () => {
  adminLoginBackToTop.classList.toggle("show", window.scrollY > 160);
});

adminLoginBackToTop?.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
