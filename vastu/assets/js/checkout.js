document.addEventListener("DOMContentLoaded", () => {
  // 1. Navbar Scroll & Scroll Progress Bar — mirrors store.js / product-detail.js
  const navbar = document.getElementById("siteNavbar");
  const progressBar = document.getElementById("scrollProgress");

  const handleScroll = () => {
    if (navbar) {
      navbar.classList.toggle("scrolled", window.scrollY > 50);
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

  // 2. Intersection Observer Reveal — mirrors store.js / product-detail.js
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

  // 4. Logout confirmation
  const checkLogout = document.getElementById("checkLogout");
  if (checkLogout) {
    checkLogout.addEventListener("click", function (e) {
      e.preventDefault();
      const confirmLogout = confirm("Are you sure you want to sign out?");
      if (confirmLogout) {
        window.location.href = "assets/api/logout.php";
      }
    });
  }

  // 5. Email change handler (preserved from original)
  document
    .getElementById("emailChangeConfirm")
    ?.addEventListener("click", () => {
      const newEmail = pendingEmailValue;
      const btn = document.getElementById("emailChangeConfirm");
      btn.disabled = true;
      btn.textContent = "Updating...";

      fetch("assets/api/update-email.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "email=" + encodeURIComponent(newEmail),
      })
        .then((r) => r.json())
        .then((data) => {
          btn.disabled = false;
          btn.textContent = "Yes, update it";
          if (data.status) {
            emailConfirmed = true;
            emailChangeModal?.hide();
          } else {
            alert(data.message || "Could not update email.");
            bookingEmailInput.value = originalEmail;
            emailChangeModal?.hide();
          }
        })
        .catch(() => {
          btn.disabled = false;
          btn.textContent = "Yes, update it";
          console.log("Network error updating email.");
          bookingEmailInput.value = originalEmail;
          emailChangeModal?.hide();
        });
    });

  // 6. Load Order Summary
  loadCheckoutSummary();

  // 7. Form submit — show confirm modal
  document
    .getElementById("checkoutForm")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      document.getElementById("confirmModal").style.display = "flex";
    });

  // 8. Cancel Order
  document.getElementById("cancelOrder").addEventListener("click", () => {
    document.getElementById("confirmModal").style.display = "none";
  });

  // 9. Close modals on backdrop click
  ["confirmModal", "successModal"].forEach((id) => {
    const modal = document.getElementById(id);
    if (modal) {
      modal.addEventListener("click", (e) => {
        if (e.target === modal) modal.style.display = "none";
      });
    }
  });

  // 10. Confirm Order — Razorpay or COD
  document
  .getElementById("confirmOrder")
  .addEventListener("click", async () => {
    document.getElementById("confirmModal").style.display = "none";

    const paymentMethod = document.querySelector("select").value;
    const processingModal = document.getElementById("orderProcessingModal");

    if (paymentMethod === "Cash on Delivery") {
      processingModal.style.display = "flex";
      try {
        await placeOrder({ payment_method: paymentMethod });
      } finally {
        processingModal.style.display = "none";
      }
      return;
    }

    try {
      const orderRes = await fetch("assets/api/create-cart-order.php");
      const orderData = await orderRes.json();

      if (!orderData.status) {
        alert(orderData.message || "Could not start payment.");
        return;
      }

      const options = {
        key: orderData.key_id,
        amount: orderData.amount,
        currency: orderData.currency,
        order_id: orderData.order_id,
        name: "Vastu Shakti Rahasya",
        description: "Store Order",
        prefill: {
          name:
            document.getElementById("checkoutFirstName").value +
            " " +
            document.getElementById("checkoutLastName").value,
          email: document.getElementById("checkoutEmail").value,
          contact: document.getElementById("checkoutPhone").value,
        },
        theme: { color: "#d8b671" },
        handler: async function (response) {
          processingModal.style.display = "flex";
          try {
            await placeOrder({
              email: document.getElementById("checkoutEmail").value,
              payment_method: paymentMethod,
              razorpay_payment_id: response.razorpay_payment_id,
              razorpay_order_id: response.razorpay_order_id,
              razorpay_signature: response.razorpay_signature,
            });
          } finally {
            processingModal.style.display = "none";
          }
        },
      };

      const rzp = new Razorpay(options);
      rzp.on("payment.failed", function (resp) {
        alert("Payment failed: " + resp.error.description);
      });
      rzp.open();
    } catch (err) {
      console.error(err);
      alert("Something went wrong starting the payment.");
    }
  });
});

// ─── Order Summary Loader ────────────────────────────────────────────────────

function loadCheckoutSummary() {
  fetch("assets/api/order-Summary.php")
    .then((res) => res.json())
    .then((res) => {
      if (!res.success) return;

      let html = "";
      let subtotal = 0;

      res.data.forEach((item) => {
        const total = item.price * item.quantity;
        subtotal += total;

        html += `
          <div class="checkout-item">
            <div>
              <div class="checkout-item-name">${item.name}</div>
              <div class="checkout-item-qty">Qty: ${item.quantity}</div>
            </div>
            <div class="checkout-item-price">₹${total}</div>
          </div>
        `;
      });

      document.getElementById("checkoutItems").innerHTML = html;
      document.getElementById("checkoutSubtotal").innerHTML = "₹" + subtotal;
      document.getElementById("checkoutTotal").innerHTML =
        "₹" + (subtotal + 150);

      // COD availability check
      toggleCOD(subtotal + 150);
    })
    .catch((err) => {
      console.error("Error loading order summary:", err);
    });
}

// ─── COD Toggle ─────────────────────────────────────────────────────────────

function toggleCOD(orderTotal) {
  const codOption = document.getElementById("codOption");
  const codHint = document.getElementById("codHint");
  const paymentSelect = document.getElementById("paymentMethod");

  const codAllowed = orderTotal > 3000;

  codOption.disabled = !codAllowed;
  codHint.style.display = codAllowed ? "none" : "block";

  // If COD was pre-selected but no longer allowed, fall back to Card Payment
  if (!codAllowed && paymentSelect.value === "Cash on Delivery") {
    paymentSelect.value = "Card Payment";
  }
}

// ─── Place Order ─────────────────────────────────────────────────────────────

function placeOrder(payload) {
  return fetch("assets/api/Order.php", {   // ← add return
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  })
    .then((res) => res.json())
    .then((res) => {
      if (res.success) {
        document.getElementById("successModal").style.display = "flex";
        document.getElementById("invoice").onclick = function () {
          window.location = "invoice.php?order_id=" + res.order_id;
        };
        if (!res.email_sent) {
          console.warn("Order placed, but confirmation email failed to send.");
        }
      } else {
        alert(res.message);
      }
    })
    .catch((error) => {
      console.error(error);
      alert("Something went wrong");
    });
}
