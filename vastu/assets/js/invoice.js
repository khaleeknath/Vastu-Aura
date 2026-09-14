document.addEventListener("DOMContentLoaded", function () {
  // ─── Navbar Scroll Behaviour ─────────────────────────────────────────────
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
      progressBar.style.width =
        (height > 0 ? (winScroll / height) * 100 : 0) + "%";
    }
  };

  window.addEventListener("scroll", handleScroll, { passive: true });
  handleScroll();

  // ─── Reveal Animation (Intersection Observer) ────────────────────────────
  const revealEls = document.querySelectorAll(".reveal-up");
  const revealObserver = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("active");
          obs.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1, rootMargin: "0px 0px -50px 0px" },
  );
  revealEls.forEach((el) => revealObserver.observe(el));

  // ─── Mobile Navbar Auto-close ─────────────────────────────────────────────
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

  // ─── Logout Confirmation ──────────────────────────────────────────────────
  const logoutBtn = document.getElementById("invoiceLogout");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function (e) {
      e.preventDefault();
      if (confirm("Are you sure you want to sign out?")) {
        window.location.href = "assets/api/logout.php";
      }
    });
  }

  // ─── Load Invoice Data ────────────────────────────────────────────────────
  loadInvoice();

  // ─── PDF Download ─────────────────────────────────────────────────────────
  const downloadBtn = document.getElementById("downloadInvoice");
  if (downloadBtn) {
    downloadBtn.addEventListener("click", function () {
      generatePDF(false);
    });
  }

  // ─── Print ────────────────────────────────────────────────────────────────
  const printBtn = document.getElementById("printInvoice");
  if (printBtn) {
    printBtn.addEventListener("click", function () {
      generatePDF(true);
    });
  }
});

// ─── Generate PDF (download or open for print) ──────────────────────────────
function generatePDF(print = false) {
  const invoice = document.getElementById("invoiceCard");

  if (!invoice) {
    alert("Invoice not found.");
    return;
  }

  const options = {
    margin: 10,
    filename: "VastuAura-Invoice.pdf",
    image: { type: "jpeg", quality: 1 },
    html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
    jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
  };

  html2pdf()
    .set(options)
    .from(invoice)
    .toPdf()
    .get("pdf")
    .then(function (pdf) {
      if (print) {
        const blob = pdf.output("blob");
        const url = URL.createObjectURL(blob);
        window.open(url);
      } else {
        pdf.save("VastuAura-Invoice.pdf");
      }
    });
}

// ─── Load Invoice from API ───────────────────────────────────────────────────
function loadInvoice() {
  const params = new URLSearchParams(window.location.search);
  const orderId = params.get("order_id");

  fetch("assets/api/invoice.php?order_id=" + orderId)
    .then((res) => res.json())
    .then((res) => {
      if (!res.success) return;

      const order = res.order;

      // Invoice number (zero-padded to 6 digits)
      document.getElementById("invoiceNumber").innerHTML =
        "Invoice #VA-" + String(order.id).padStart(6, "0");

      // Date, customer, email, status
      document.getElementById("invoiceDate").innerHTML = order.date;
      document.getElementById("invoiceCustomer").innerHTML = order.customer;
      document.getElementById("invoiceEmail").innerHTML = order.email;
      document.getElementById("invoiceStatus").innerHTML = order.payment_status;

      // Line items
      let html = "";
      let subtotal = 0;

      res.items.forEach((item) => {
        const lineTotal = item.price * item.qty;
        subtotal += lineTotal;

        html += `
          <div class="invoice-item-row">
            <span class="item-name">${item.name}</span>
            <span class="item-qty">₹${item.price} × ${item.qty}</span>
            <strong class="item-total">₹${lineTotal}</strong>
          </div>
        `;
      });

      document.getElementById("invoiceItems").innerHTML = html;

      // Totals
      document.getElementById("invoiceSubtotal").innerHTML = "₹" + subtotal;
      document.getElementById("invoiceTotal").innerHTML =
        "₹" + (subtotal + 150);
    })
    .catch((err) => {
      console.error("Error loading invoice:", err);
    });
}
