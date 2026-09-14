document.addEventListener("DOMContentLoaded", () => {
  // 1. Sleek Navbar & Scroll Progress Handlers
  const navbar = document.getElementById("siteNavbar");
  const progressBar = document.getElementById("scrollProgress");
  const backToTopBtn = document.getElementById("backToTop");

  const handleScroll = () => {
    if (navbar) {
      if (window.scrollY > 50) {
        navbar.classList.add("scrolled");
      } else {
        navbar.classList.remove("scrolled");
      }
    }

    // Scroll Progress Bar calculation
    if (progressBar) {
      const winScroll =
        document.body.scrollTop || document.documentElement.scrollTop;
      const height =
        document.documentElement.scrollHeight -
        document.documentElement.clientHeight;
      const scrolled = height > 0 ? (winScroll / height) * 100 : 0;
      progressBar.style.width = scrolled + "%";
    }

    // Back to Top Button visibility
    if (backToTopBtn) {
      if (window.scrollY > 280) {
        backToTopBtn.classList.add("show");
      } else {
        backToTopBtn.classList.remove("show");
      }
    }
  };

  window.addEventListener("scroll", handleScroll, { passive: true });
  handleScroll(); // init on load

  // Back to Top Action
  if (backToTopBtn) {
    backToTopBtn.addEventListener("click", () => {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  // 2. Intersection Observer Reveal Logic (Matches Store Page)
  const revealElements = document.querySelectorAll(".reveal-up");
  const revealOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };

  const revealObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("active");
        observer.unobserve(entry.target);
      }
    });
  }, revealOptions);

  revealElements.forEach((el) => revealObserver.observe(el));

  // 3. Auto-close mobile navbar on link click
  const navLinks = document.querySelectorAll(
    ".navbar-nav .nav-link:not(.dropdown-toggle)",
  );
  const navbarCollapse = document.getElementById("siteNav");

  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      if (navbarCollapse && navbarCollapse.classList.contains("show")) {
        const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
        if (bsCollapse) bsCollapse.hide();
      }
    });
  });

  // 4. Testimonials Filtering Logic
  const filterBtns = document.querySelectorAll(".btn-filter");
  const testimonialItems = document.querySelectorAll(".testimonial-item");

  filterBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      // Manage active states
      filterBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      const filter = btn.dataset.filter;

      // Filter grid items natively
      testimonialItems.forEach((item) => {
        const isVisible = filter === "all" || item.dataset.group === filter;
        item.style.display = isVisible ? "block" : "none";
      });
    });
  });
});
