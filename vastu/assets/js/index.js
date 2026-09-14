document.addEventListener("DOMContentLoaded", () => {
  // 1. Sleek Navbar & Scroll Progress
  const navbar = document.getElementById("siteNavbar");
  const progressBar = document.getElementById("scrollProgress");
  const popupOverlay = document.getElementById('subscribePopupOverlay');
  const closePopupBtn = document.getElementById('closeSubscribePopup');

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
  const revealElements = document.querySelectorAll(
    ".reveal-up, .reveal-item, .reveal-fade",
  );

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

  const form = document.querySelector('.bespoke-subscribe');

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const emailInput = form.querySelector('input[type="email"]');
    const email = emailInput.value.trim();
    const submitBtn = form.querySelector('button[type="submit"]');

    if (!email) {
      alert("Please enter your email address.");
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = "Subscribing...";

    const formData = new FormData();
    formData.append('email', email);

    fetch('assets/api/subscribe.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          emailInput.value = "";
          popupOverlay.classList.add('active');
        } else {
          showErrorPopup(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showErrorPopup("Something went wrong. Please try again.");
      })
      .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = "Subscribe";
      });

    
  });

  closePopupBtn.addEventListener('click', function () {
    popupOverlay.classList.remove('active');
  });

   // Close popup when clicking outside the box
   popupOverlay.addEventListener('click', function (e) {
    if (e.target === popupOverlay) {
      popupOverlay.classList.remove('active');
    }
  });

  // Simple fallback error popup (reuses same overlay, just changes text)
  function showErrorPopup(message) {
    const box = popupOverlay.querySelector('.subscribe-popup-box');
    box.querySelector('h3').textContent = "Oops!";
    box.querySelector('p').textContent = message;
    box.querySelector('.subscribe-popup-icon').textContent = "!";
    popupOverlay.classList.add('active');
  }

});



