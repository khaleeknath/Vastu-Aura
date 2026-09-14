document.addEventListener("DOMContentLoaded", function () {
  // 1. Navbar Scroll & Progress Bar — mirrors store.js
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

  // 2. Intersection Observer Reveal — mirrors store.js
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

  // 3. Auto-close mobile navbar on link click — mirrors store.js
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

  // 4. Modal elements
  const addToCartBtn = document.getElementById("detailAddToCart");
  const confirmModal = document.getElementById("productconfirmModal");
  const successModal = document.getElementById("successModal");
  const cancelAction = document.getElementById("cancelOrder");
  const confirmAction = document.getElementById("confirmOrder");
  const successAction = document.getElementById("continueShopping");

  // Open Confirmation Modal
  if (addToCartBtn) {
    addToCartBtn.addEventListener("click", () => {
      if (confirmModal) confirmModal.style.display = "flex";
    });
  }

  // Cancel
  if (cancelAction) {
    cancelAction.addEventListener("click", () => {
      if (confirmModal) confirmModal.style.display = "none";
    });
  }

  // Close modals when clicking overlay backdrop
  [confirmModal, successModal].forEach((modal) => {
    if (modal) {
      modal.addEventListener("click", (e) => {
        if (e.target === modal) {
          modal.style.display = "none";
        }
      });
    }
  });

  // Confirm Add To Cart
  if (confirmAction) {
    confirmAction.addEventListener("click", () => {
      if (confirmModal) confirmModal.style.display = "none";

      fetch("assets/api/add-to-cart.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          product_id: currentProduct.data.id,
          quantity: qty,
        }),
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.success) {
            if (successModal) {
              const h3 = successModal.querySelector("h3");
              const p = successModal.querySelector("p");
              if (h3) h3.innerHTML = "Added to Cart!";
              if (p)
                p.innerHTML =
                  "Your product has been added successfully to your cart.";
              successModal.style.display = "flex";
            }
          } else {
            alert(res.message);
          }
        })
        .catch((error) => {
          console.error(error);
          alert("Something went wrong");
        });
    });
  }

  // Success — return to store
  if (successAction) {
    successAction.addEventListener("click", () => {
      window.location.href = "store.php";
    });
  }
});

// 5. Load Product from API
document.addEventListener("DOMContentLoaded", loadProduct);

let currentProduct = null;

function loadProduct() {
  const params = new URLSearchParams(window.location.search);
  const productId = params.get("id");

  if (!productId) {
    alert("Product ID Missing");
    return;
  }

  fetch(`assets/api/productDetail.php?id=${productId}`)
    .then((response) => response.json())
    .then((result) => {
      console.log(result);
      currentProduct = result;
      const product = result.data;

      if (!product) {
        alert("Product Not Found");
        return;
      }

      // Update breadcrumb with product name
      const breadcrumb = document.getElementById("breadcrumbProduct");
      if (breadcrumb) breadcrumb.innerText = product.name;

      // Populate category eyebrow
      const categoryEl = document.getElementById("productCategory");
      if (categoryEl) categoryEl.innerText = product.category_name;

      // Populate product name
      const nameEl = document.getElementById("productName");
      if (nameEl) nameEl.innerText = product.name;

      // Populate product image
      const imgEl = document.getElementById("productImage");
      if (imgEl) {
        if (product.image) {
          imgEl.src = "assets/uploads/products/" + product.image;
          imgEl.alt = product.name;
        } else {
          // Fallback placeholder consistent with store.js category logic
          const cat = product.category_name
            ? product.category_name.toLowerCase()
            : "";
          let fallbackImg =
            "https://images.unsplash.com/photo-1596526131083-e8c633c948d2?auto=format&fit=crop&q=80";
          if (cat.includes("bracelet")) {
            fallbackImg =
              "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?auto=format&fit=crop&q=80";
          } else if (cat.includes("yantra")) {
            fallbackImg =
              "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&q=80";
          }
          imgEl.src = fallbackImg;
          imgEl.alt = product.name;
        }
      }

      // Populate description
      const descEl = document.getElementById("productDescription");
      if (descEl) descEl.innerText = product.description;

      // Populate price
      const priceEl = document.getElementById("productPrice");
      if (priceEl) priceEl.innerText = "₹" + product.price;

      // Populate product notes
      const notesEl = document.getElementById("productNotes");
      if (notesEl) notesEl.innerText = product.description;
    })
    .catch((error) => {
      console.error(error);
    });
}

// 6. Quantity Controls
let qty = 1;

const qtyValue = document.getElementById("qtyValue");
const qtyPlus = document.getElementById("qtyPlus");
const qtyMinus = document.getElementById("qtyMinus");

if (qtyPlus) {
  qtyPlus.addEventListener("click", () => {
    qty++;
    if (qtyValue) qtyValue.innerText = qty;
  });
}

if (qtyMinus) {
  qtyMinus.addEventListener("click", () => {
    if (qty > 1) {
      qty--;
      if (qtyValue) qtyValue.innerText = qty;
    }
  });
}
