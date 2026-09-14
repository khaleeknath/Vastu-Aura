document.addEventListener("DOMContentLoaded", () => {
  // 1. Sleek Navbar & Scroll Progress
  const navbar = document.getElementById("siteNavbar");
  const progressBar = document.getElementById("scrollProgress");

  const handleScroll = () => {
    if (navbar) {
      if (window.scrollY > 50) {
        navbar.classList.add("scrolled");
      } else {
        navbar.classList.remove("scrolled");
      }
    }

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
  handleScroll();

  // 2. High-End Intersection Observer Reveal Logic
  const revealOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
  const revealObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("active");
        observer.unobserve(entry.target);
      }
    });
  }, revealOptions);

  function bindRevealElements() {
    const revealElements = document.querySelectorAll(".reveal-up:not(.active)");
    revealElements.forEach((el) => revealObserver.observe(el));
  }

  bindRevealElements();

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

  // 4. Logout Logic (Preserved Functionality)
  const logoutBtn = document.getElementById("appointmentLogout");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function (e) {
      e.preventDefault();
      let confirmLogout = confirm("Are you sure you want to log out?");
      if (confirmLogout) {
        window.location.href = "assets/api/logout.php";
      }
    });
  }

  // 5. Fetch and Render Appointments (Preserved API Connection + Luxury UI)
  loadAppointments();

    // 6. Appointment Details Modal Logic
    const apptDetailsModalEl = document.getElementById("appointmentDetailsModal");
    const apptDetailsModal = apptDetailsModalEl
      ? new bootstrap.Modal(apptDetailsModalEl)
      : null;
  
    document.getElementById("appointmentsContainer").addEventListener("click", (e) => {
      const btn = e.target.closest(".view-appt-details");
      if (!btn) return;
      openAppointmentDetails(btn.dataset.apptId);
    });
  
    function openAppointmentDetails(appointmentId) {
      const loadingEl = document.getElementById("apptDetailsLoading");
      const contentEl = document.getElementById("apptDetailsContent");
      const errorEl = document.getElementById("apptDetailsError");
      const commentWrap = document.getElementById("apptCommentWrap");
  
      loadingEl.classList.remove("d-none");
      contentEl.classList.add("d-none");
      errorEl.classList.add("d-none");
      commentWrap.classList.add("d-none");
      errorEl.textContent = "";
  
      apptDetailsModal.show();
  
      fetch(`assets/api/appointment-details.php?id=${encodeURIComponent(appointmentId)}`)
        .then((res) => res.json())
        .then((response) => {
          loadingEl.classList.add("d-none");
  
          if (!response.success) {
            errorEl.textContent = response.message || "Unable to load appointment details.";
            errorEl.classList.remove("d-none");
            return;
          }
  
          const a = response.appointment;
          const fallback = (val) => (val === null || val === undefined || val === "" ? "N/A" : val);
  
          document.getElementById("apptId").textContent = "VA-" + String(a.id).padStart(6, "0");
          document.getElementById("apptName").textContent = fallback(a.name);
          document.getElementById("apptEmail").textContent = fallback(a.email);
          document.getElementById("apptMobile").textContent = fallback(a.mobile);
          document.getElementById("apptType").textContent = fallback(a.consultation_type);
          document.getElementById("apptDate").textContent = fallback(a.preferred_date);
          document.getElementById("apptTime").textContent = fallback(a.preferred_time);
          document.getElementById("apptPropertyType").textContent = fallback(a.property_type);
          document.getElementById("apptBhk").textContent = fallback(a.bhk_type);
          document.getElementById("apptSqft").textContent = a.sqft ? `${a.sqft} sq.ft` : "N/A";
          document.getElementById("apptUnit").textContent = fallback(a.unit_number);
          document.getElementById("apptAddress").textContent = fallback(a.address);
          document.getElementById("apptDistance").textContent = a.distance_km ? `${a.distance_km} km` : "N/A";
          document.getElementById("apptStatus").textContent = fallback(a.status);
          document.getElementById("apptPaymentStatus").textContent = fallback(a.payment_status);
          document.getElementById("apptPaymentRef").textContent = fallback(a.razorpay_payment_id);
          document.getElementById("apptAmount").textContent = a.amount ? `₹${a.amount}` : "N/A";
  
          if (a.comment) {
            document.getElementById("apptComment").textContent = a.comment;
            commentWrap.classList.remove("d-none");
          }
  
          contentEl.classList.remove("d-none");
        })
        .catch((err) => {
          console.error("Error loading appointment details:", err);
          loadingEl.classList.add("d-none");
          errorEl.textContent = "Something went wrong while loading appointment details.";
          errorEl.classList.remove("d-none");
        });
    }
    
  function loadAppointments() {
    fetch("assets/api/myappointments.php")
      .then((res) => res.json())
      .then((res) => {
        const container = document.getElementById("appointmentsContainer");

        if (!res.success) {
          container.innerHTML = `
            <div class="luxury-alert text-center text-danger p-4 border border-danger rounded bg-white">
                ${res.message}
            </div>
          `;
          return;
        }

        // Empty State
        if (res.appointments.length === 0) {
          container.innerHTML = `
            <div class="empty-appointments-display reveal-up active text-center py-5">
                <span class="d-block text-gold fs-2 mb-3">✧</span>
                <h3 class="cinzel-heading text-dark-900 mb-2">No Appointments Found.</h3>
                <p class="font-montserrat text-muted mb-4">You have not scheduled any spatial or astrological alignment sessions yet.</p>
                <a href="booking.php" class="btn-luxury-solid">Reserve a Session</a>
            </div>
          `;
          return;
        }

        // Render Appointment Cards
        let html = "";
        res.appointments.forEach((app, index) => {
          let badgeClass = "";
          switch (app.status.toLowerCase()) {
            case "pending":
              badgeClass = "badge-gold";
              break;
            case "approved":
              badgeClass = "badge-purple";
              break;
            case "completed":
              badgeClass = "badge-success";
              break;
            case "cancelled":
              badgeClass = "badge-danger";
              break;
            case "rejected":
              badgeClass = "badge-dark";
              break;
            default:
              badgeClass = "badge-dark";
          }

          const delayStyle = `transition-delay: ${index * 0.1}s;`;

          html += `
            <div class="luxury-appointment-card bg-white p-4 p-md-5 reveal-up" style="${delayStyle}">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom border-light">
                    <div>
                        <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-1 d-block">Consultation Request</span>
                        <h4 class="cinzel-heading text-dark-900 mb-0 fs-3">No. VA-${String(app.id).padStart(6, "0")}</h4>
                    </div>
                    <div class="mt-3 mt-md-0 text-md-end">
                        <span class="luxury-badge ${badgeClass}">${app.status}</span>
                    </div>
                </div>
                
                <div class="row g-4 font-montserrat text-muted">
                    <div class="col-12 col-md-4">
                        <small class="text-uppercase tracking-wide d-block mb-1" style="font-size: 0.7rem;">Service Type</small>
                        <strong class="text-dark-900 fw-medium d-block" style="font-size: 0.95rem;">${app.consultation_type}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-uppercase tracking-wide d-block mb-1" style="font-size: 0.7rem;">Preferred Date</small>
                        <strong class="text-dark-900 fw-medium d-block" style="font-size: 0.95rem;">${app.preferred_date}</strong>
                    </div>
                    <div class="col-6 col-md-2">
                        <small class="text-uppercase tracking-wide d-block mb-1" style="font-size: 0.7rem;">Preferred Time</small>
                        <strong class="text-dark-900 fw-medium d-block" style="font-size: 0.95rem;">${app.preferred_time}</strong>
                    </div>
                    <div class="col-12 col-md-3 text-md-end d-flex align-items-center justify-content-md-end mt-4 mt-md-0 pt-2 pt-md-0">
                    <button type="button" class="btn-luxury-outline text-dark-900 w-100 w-md-auto view-appt-details" data-appt-id="${app.id}" style="padding: 10px 20px;">View Itinerary</button>
                    </div>
                </div>
            </div>
          `;
        });

        container.innerHTML = html;
        bindRevealElements();
      })
      .catch((err) => {
        console.error("Error Loading Appointments:", err);
      });
  }
});
