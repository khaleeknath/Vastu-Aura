



document.addEventListener("DOMContentLoaded", () => {

  fetch("assets/api/productList.php")
  .then(response => response.json())
  .then(result => {
      // const cartCountEl = document.getElementById("cartCount");

      // if (cartCountEl) {
      //     cartCountEl.innerText = result.cartCount || 0;
      // }
  })
  .catch(error => {
      console.error("Error:", error);
  });

  // 1. Sleek Navbar & Scroll Progress Handlers
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

  // 2. Intersection Observer Reveal Logic
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

  // 4. Track currently selected category (0 = All)
  let currentCategoryId = 0;

  // 5. Load Categories via API, then load the initial product list
  loadCategories();

  function loadCategories() {
    fetch("assets/api/categoryList.php")
      .then((response) => response.json())
      .then((result) => {
        const filterBar = document.getElementById("categoryFilterBar");

        // Always start with the "All" option, then append DB categories
        let html = `<button class="btn-filter active" data-id="0">The Complete Collection</button>`;

        (result.data || []).forEach((cat) => {
          html += `<button class="btn-filter" data-id="${cat.id}">${cat.name}</button>`;
        });

        if (filterBar) filterBar.innerHTML = html;

        bindFilterTriggers();
        loadProducts(currentCategoryId);
      })
      .catch((error) => {
        console.error("ERROR Loading Categories from API:", error);
        // Even if categories fail, still show all products
        loadProducts(currentCategoryId);
      });
  }

  // 6. Load Products via API (now filterable by category_id)
  function loadProducts(categoryId = 0) {
    const url =
      categoryId > 0
        ? `assets/api/productList.php?category_id=${categoryId}`
        : `assets/api/productList.php`;

    fetch(url)
      .then((response) => response.json())
      .then((result) => {
        // Update Cart Badge from backend response
        const cartCountEl = document.getElementById("cartCount");
        if (cartCountEl) {
          cartCountEl.innerText = result.cartCount || 0;
        }

        let html = "";

        result.data.forEach((product, index) => {
          // Dynamic delay for smooth staggering effect on load
          const delayClass = `stagger-${(index % 3) + 1}`;

          // Retrieve the filename from the backend API response.
          let rawImgUrl =
            product.image ||
            product.image_url ||
            product.img ||
            product.product_image;
          let imgUrl = "";

          // If the database has an image, prepend the actual directory path where the images are stored
          if (rawImgUrl && rawImgUrl.trim() !== "") {
            let safeFilename = encodeURI(rawImgUrl.trim())
              .replace(/'/g, "%27")
              .replace(/"/g, "%22");
            // Map to the actual physical path shown in your project structure
            imgUrl = `assets/uploads/products/${safeFilename}`;
          } else {
            // Fallback to placeholder ONLY if the specific product has no image (NULL/empty) in the database
            imgUrl =
              "https://images.unsplash.com/photo-1596526131083-e8c633c948d2?auto=format&fit=crop&q=80";
            const cat = product.category_name
              ? product.category_name.toLowerCase()
              : "";

            if (cat.includes("bracelet")) {
              imgUrl =
                "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?auto=format&fit=crop&q=80";
            } else if (cat.includes("yantra")) {
              imgUrl =
                "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&q=80";
            }
          }

          // HTML structure dynamically mapping original database fields into bespoke UI classes
          html += `
            <div class="col-md-6 col-lg-4 reveal-up active ${delayClass}">
              <div class="luxury-product-card">
                <a href="product-detail.php?id=${product.id}" class="product-image-link">
                  <div class="product-image-wrapper">
                    <div class="product-image" style="background-image: url('${imgUrl}');"></div>
                    <div class="product-overlay">
                      <span class="view-text font-cinzel">View Details</span>
                    </div>
                  </div>
                </a>
                <div class="product-info">
                  <span class="product-category text-gold font-montserrat">${product.category_name || "Curated"}</span>
                  <h3 class="product-title cinzel-heading"><a href="product-detail.php?id=${product.id}">${product.name}</a></h3>
                  <p class="product-desc font-montserrat text-muted small">${product.description || ""}</p>
                  <div class="product-footer d-flex justify-content-between align-items-center">
                    <span class="product-price font-montserrat fw-bold text-dark-900">₹${product.price}</span>
                    <button class="btn-add-cart toast-trigger" aria-label="Add to Cart" data-id="${product.id}">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 5v14M5 12h14"/></svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          `;
        });

        if (result.data.length === 0) {
          html = `
            <div class="col-12 text-center py-5">
              <p class="font-montserrat text-muted mb-0">No products found in this category.</p>
            </div>
          `;
        }

        const grid = document.getElementById("productGrid");
        if (grid) grid.innerHTML = html;

        // Re-bind toast triggers for dynamically loaded buttons
        bindToastTriggers();
      })
      .catch((error) => {
        console.error("ERROR Loading Products from API:", error);
      });
  }

  // 7. Elegant Toast Notification for Cart Actions
  function bindToastTriggers() {
    const toastTriggers = document.querySelectorAll(".toast-trigger");
    const toastBanner = document.getElementById("cartToast");

    toastTriggers.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();

        if (toastBanner) {
          toastBanner.classList.remove("d-none");
          toastBanner.classList.add("show");

          // Update dynamic cart indicator locally on visual layer
          const cartCountEl = document.getElementById("cartCount");
          if (cartCountEl) {
            cartCountEl.innerText = parseInt(cartCountEl.innerText) + 1;
          }

          // Hide UI element after 3 seconds
          setTimeout(() => {
            toastBanner.classList.remove("show");
          }, 3000);
        }
      });
    });
  }

  // 8. Category Filter Buttons Interaction Handling (rebound whenever categories reload)
  function bindFilterTriggers() {
    const filterBtns = document.querySelectorAll(".btn-filter");
    filterBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        filterBtns.forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");

        const categoryId = parseInt(btn.getAttribute("data-id"), 10) || 0;
        currentCategoryId = categoryId;
        loadProducts(currentCategoryId);
      });
    });
  }
});