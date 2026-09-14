document.addEventListener("DOMContentLoaded", () => {
  // 1. Sleek Navbar & Scroll Progress (Matched to Master System)
  const form      = document.getElementById('contactForm');
  const alertBox  = document.getElementById('contactAlert');
  const alertMsg  = alertBox ? alertBox.querySelector('.alert-message') : null;
  const navbar = document.getElementById("siteNavbar");
  const progressBar = document.getElementById("scrollProgress");

  function showAlert(message, isError) {
    if (!alertBox || !alertMsg) return;
    alertMsg.textContent = message;
    alertBox.classList.remove('d-none', 'alert-success', 'alert-error');
    alertBox.classList.add(isError ? 'alert-error' : 'alert-success');
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  const handleScroll = () => {
    // Navbar Shrink
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
  };

  window.addEventListener("scroll", handleScroll, { passive: true });
  handleScroll(); // Init on load

  // 2. High-End Intersection Observer Reveal Logic
  const revealElements = document.querySelectorAll(".reveal-up");
  const revealOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  };

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

  // 4. Contact Form Submission Logic
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    const formData = new FormData(form);
    console.log(formData)
    fetch('assets/api/contact.php', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
      .then((res) => res.json())
      .then((data) => {
        showAlert(data.message, !data.success);
        if (data.success) {
          form.reset();
        }
      })
      .catch(() => {
        showAlert('Something went wrong. Please check your connection and try again.', true);
      })
      .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      });
  });
});