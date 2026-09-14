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

  // 4. Logout Logic (Preserved Backend Logic)
  const logoutBtn = document.getElementById("myOrderLogout");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function (e) {
      e.preventDefault();
      let confirmLogout = confirm("Are you sure you want to log out?");
      if (confirmLogout) {
        window.location.href = "assets/api/logout.php";
      }
    });
  }

  // 5. Fetch and Render Orders (Preserved API Connection + Luxury UI)
  loadOrders();

    // 6. Order Details Modal Logic
    const orderDetailsModalEl = document.getElementById("orderDetailsModal");
    const orderDetailsModal = orderDetailsModalEl
      ? new bootstrap.Modal(orderDetailsModalEl)
      : null;
  
    // Event delegation: container is re-rendered, so listen on a static parent
    document.getElementById("ordersContainer").addEventListener("click", (e) => {
      const btn = e.target.closest(".view-order-details");
      if (!btn) return;
  
      const orderId = btn.dataset.orderId;
      openOrderDetails(orderId);
    });
  
    function openOrderDetails(orderId) {
      const loadingEl = document.getElementById("orderDetailsLoading");
      const contentEl = document.getElementById("orderDetailsContent");
      const errorEl = document.getElementById("orderDetailsError");
  
      // Reset state
      loadingEl.classList.remove("d-none");
      contentEl.classList.add("d-none");
      errorEl.classList.add("d-none");
      errorEl.textContent = "";
  
      orderDetailsModal.show();
  
      fetch(`assets/api/order-details.php?order_id=${encodeURIComponent(orderId)}`)
        .then((res) => res.json())
        .then((response) => {
          loadingEl.classList.add("d-none");
  
          if (!response.success) {
            errorEl.textContent = response.message || "Unable to load order details.";
            errorEl.classList.remove("d-none");
            return;
          }
  
          const order = response.order;
  
          document.getElementById("detailOrderId").textContent =
            "VA-" + String(order.id).padStart(6, "0");
          document.getElementById("detailOrderDate").textContent = order.order_date;
          document.getElementById("detailOrderStatus").textContent = order.order_status;
          document.getElementById("detailPaymentMethod").textContent =
            order.payment_method || "N/A";
          document.getElementById("detailPaymentStatus").textContent =
            order.payment_status || "N/A";
          document.getElementById("detailTransactionId").textContent =
            order.transaction_id || "N/A";
          document.getElementById("detailTotalAmount").textContent =
            "₹" + order.total_amount;
            
  
          const itemsBody = document.getElementById("detailItemsBody");
          itemsBody.innerHTML = order.items
            .map(
              (item) => `
                <tr class="border-bottom border-light border-opacity-10">
                  <td class="d-flex align-items-center gap-3 py-2">
                    <span>${item.name}</span>
                  </td>
                  <td class="text-center">${item.quantity}</td>
                  <td class="text-end">₹${item.price}</td>
                  <td class="text-end text-gold">₹${item.subtotal}</td>
                </tr>
              `
            )
            .join("");
  
          contentEl.classList.remove("d-none");
        })
        .catch((error) => {
          console.error("Error loading order details:", error);
          loadingEl.classList.add("d-none");
          errorEl.textContent = "Something went wrong while loading order details.";
          errorEl.classList.remove("d-none");
        });
    }
    

  function loadOrders() {
    fetch("assets/api/myorder.php")
      .then((response) => response.json())
      .then((response) => {
        const container = document.getElementById("ordersContainer");

        if (!response.success) {
          container.innerHTML = `
            <div class="luxury-alert text-center text-danger p-4 border border-danger rounded bg-white">
                ${response.message}
            </div>
          `;
          return;
        }

        // Empty State
        if (response.orders.length === 0) {
          container.innerHTML = `
            <div class="empty-orders-display reveal-up active text-center py-5">
                <span class="d-block text-gold fs-2 mb-3">✧</span>
                <h3 class="cinzel-heading text-dark-900 mb-2">No Order History Found.</h3>
                <p class="font-montserrat text-muted mb-4">You have not yet acquired any artifacts from our atelier.</p>
                <a href="store.php" class="btn-luxury-solid">Explore Store</a>
            </div>
          `;
          return;
        }

        // Render Order Cards
        let html = "";
        response.orders.forEach((order, index) => {
          let badgeClass = "";
          switch (order.order_status.toLowerCase()) {
            case "pending":
              badgeClass = "badge-gold";
              break;
            case "confirmed":
            case "processing":
            case "shipped":
              badgeClass = "badge-purple";
              break;
            case "delivered":
              badgeClass = "badge-success";
              break;
            case "cancelled":
              badgeClass = "badge-danger";
              break;
            default:
              badgeClass = "badge-dark";
          }

          const delayStyle = `transition-delay: ${index * 0.1}s;`;

          html += `
            <div class="luxury-order-card bg-white p-4 p-md-5 reveal-up" style="${delayStyle}">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom border-light">
                    <div>
                        <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-1 d-block">Order Receipt</span>
                        <h4 class="cinzel-heading text-dark-900 mb-0 fs-3">No. VA-${String(order.id).padStart(6, "0")}</h4>
                    </div>
                    <div class="mt-3 mt-md-0 text-md-end">
                        <span class="luxury-badge ${badgeClass}">${order.order_status}</span>
                    </div>
                </div>
                
                <div class="row g-4 font-montserrat text-muted">
                    <div class="col-6 col-md-3">
                        <small class="text-uppercase tracking-wide d-block mb-1" style="font-size: 0.7rem;">Date</small>
                        <strong class="text-dark-900 fw-medium d-block" style="font-size: 0.95rem;">${order.order_date}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-uppercase tracking-wide d-block mb-1" style="font-size: 0.7rem;">Payment</small>
                        <strong class="text-dark-900 fw-medium d-block" style="font-size: 0.95rem;">${order.payment_method}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-uppercase tracking-wide d-block mb-1" style="font-size: 0.7rem;">Total Amount</small>
                        <strong class="text-gold fw-bold d-block" style="font-size: 1.1rem;">₹${order.total_amount}</strong>
                    </div>
                    <div class="col-12 col-md-3 text-md-end d-flex align-items-center justify-content-md-end mt-4 mt-md-0 pt-2 pt-md-0">
                    <button type="button" class="btn-luxury-outline text-dark-900 w-100 w-md-auto view-order-details" data-order-id="${order.id}" style="padding: 10px 20px;">View Details</button>
                    </div>
                </div>
            </div>
          `;
        });

        container.innerHTML = html;
        bindRevealElements();
      })
      .catch((error) => {
        console.error("Error Loading Orders:", error);
      });
  }
});
