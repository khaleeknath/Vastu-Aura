document.addEventListener("DOMContentLoaded", () => {
  // 1. Sleek Navbar & Scroll Progress (Preserved from Frontend)
  const navbar = document.getElementById("siteNavbar");
  const progressBar = document.getElementById("scrollProgress");

  const handleScroll = () => {
    if (navbar) {
      if (window.scrollY > 50) navbar.classList.add("scrolled");
      else navbar.classList.remove("scrolled");
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

  // 2. High-End Intersection Observer Reveal Logic (Preserved from Frontend)
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

  // 3. Auto-close mobile navbar on link click (Preserved from Frontend)
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

  // =========================================================================
  // PRESVERED BACKEND LOGIC STARTS HERE
  // =========================================================================

  // 4. Time Slot Loading Logic
  const dateInput = document.getElementById("bookingDate");

  function loadSlotsForDate(selectedDate) {
    const dropdown = document.getElementById("preferred_time");
    const noSlotsMsg = document.getElementById("noSlotsMsg");

    dropdown.innerHTML =
      '<option disabled selected value="">-- Select Preferred Time --</option>';
    dropdown.disabled = false;
    noSlotsMsg.classList.add("d-none");

    fetch(
      `assets/api/booking.php?action=get_booking_slots&date=${encodeURIComponent(selectedDate)}`,
    )
      .then((response) => response.json())
      .then((result) => {
        if (result.status === "success") {
          if (result.data.length === 0) {
            dropdown.disabled = true;
            noSlotsMsg.classList.remove("d-none");
            return;
          }
          result.data.forEach((slot) => {
            const option = document.createElement("option");
            option.value = slot.value;
            option.textContent = slot.value;
            dropdown.appendChild(option);
          });
          dropdown.selectedIndex = 1;
        }
      })
      .catch((error) => console.error("Error loading booking slots:", error));
  }

  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  const yyyy = tomorrow.getFullYear();
  const mm = String(tomorrow.getMonth() + 1).padStart(2, "0");
  const dd = String(tomorrow.getDate()).padStart(2, "0");
  const tomorrowStr = `${yyyy}-${mm}-${dd}`;

  if (dateInput) {
    dateInput.min = tomorrowStr;
    dateInput.value = tomorrowStr;
    loadSlotsForDate(tomorrowStr);

    dateInput.addEventListener("change", function () {
      if (this.value) loadSlotsForDate(this.value);
    });
  }

  // 5. Booking Form Validation & Modal Population
  const form = document.getElementById("bookingForm");
  const openModalBtn = document.getElementById("openConfirmModal");

  if (openModalBtn && form) {
    openModalBtn.addEventListener("click", function () {
      const timeSelect = document.getElementById("preferred_time");
      const noSlotsMsg = document.getElementById("noSlotsMsg");

      if (!timeSelect.value || timeSelect.disabled) {
        noSlotsMsg.classList.remove("d-none");
        timeSelect.focus();
        return;
      }

      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      document.getElementById("confirmName").innerText =
        document.querySelector('[name="name"]').value;
      document.getElementById("confirmEmail").innerText =
        document.querySelector('[name="email"]').value;
      document.getElementById("confirmMobile").innerText =
        document.querySelector('[name="mobile"]').value;
      document.getElementById("confirmDate").innerText = document.querySelector(
        '[name="preferred_date"]',
      ).value;
      document.getElementById("confirmTime").innerText = document.querySelector(
        '[name="preferred_time"]',
      ).value;
      document.getElementById("confirmType").innerText = document.querySelector(
        '[name="consultation_type"]',
      ).value;
      document.getElementById("confirmAddress").innerText =
        document.querySelector('[name="address"]').value;
      document.getElementById("confirmUnit").innerText =
        document.querySelector('[name="unit_number"]').value || "—";

      const propType = document.querySelector('[name="property_type"]').value;
      let propertyDetailsText;
      if (propType === "Commercial") {
        const sqft = document.getElementById("sqftInput").value || "?";
        propertyDetailsText = "Commercial — " + sqft + " sq.ft";
      } else {
        const bhkSelect = document.getElementById("bhkType");
        propertyDetailsText =
          "Flat — " + bhkSelect.options[bhkSelect.selectedIndex].text;
      }
      document.getElementById("confirmPropertyDetails").innerText =
        propertyDetailsText;

      document.getElementById("confirmDistance").innerText =
        document.getElementById("distanceValue").textContent;

      let amountCalculation = document
        .getElementById("amountValue")
        .textContent.trim();
      let amount = parseFloat(amountCalculation.replace(/[₹,]/g, ""));
      let halfAmount = amount / 2;

      document.getElementById("confirmAmount").innerText =
        `₹${halfAmount.toLocaleString("en-IN")}`;

      const modal = new bootstrap.Modal(
        document.getElementById("confirmBookingModal"),
      );
      modal.show();
    });
  }

  // 6. Terms Validation for Payment
  const agreeTermsCheckbox = document.getElementById("agreeTerms");
  const agreeErrorMsg = document.getElementById("agreeError");
  const confirmSubmitBtn = document.getElementById("confirmSubmit");

  if (agreeTermsCheckbox) {
    agreeTermsCheckbox.addEventListener("change", function () {
      confirmSubmitBtn.disabled = !this.checked;
      if (this.checked) agreeErrorMsg.classList.add("d-none");
    });

    document
      .getElementById("confirmBookingModal")
      .addEventListener("show.bs.modal", function () {
        agreeTermsCheckbox.checked = false;
        confirmSubmitBtn.disabled = true;
        agreeErrorMsg.classList.add("d-none");
      });
  }

  // 7. Email Change Detection Logic
  const bookingEmailInput = document.getElementById("bookingEmail");
  const originalEmail = (bookingEmailInput?.dataset.original || "")
    .trim()
    .toLowerCase();
  let emailConfirmed = false;
  let pendingEmailValue = null;

  const emailChangeModalEl = document.getElementById("emailChangeModal");
  const emailChangeModal = emailChangeModalEl
    ? new bootstrap.Modal(emailChangeModalEl)
    : null;

  function emailNeedsConfirmation(val) {
    return (
      val.trim().toLowerCase() !== originalEmail &&
      !(emailConfirmed && pendingEmailValue === val.trim())
    );
  }

  function askAboutEmailChange(newVal) {
    document.getElementById("emailOld").textContent = originalEmail;
    document.getElementById("emailNew").textContent = newVal;
    pendingEmailValue = newVal;
    emailChangeModal?.show();
  }

  bookingEmailInput?.addEventListener("blur", () => {
    const val = bookingEmailInput.value.trim();
    if (val && emailNeedsConfirmation(val)) askAboutEmailChange(val);
  });

  document
    .getElementById("emailChangeCancel")
    ?.addEventListener("click", () => {
      bookingEmailInput.value = originalEmail;
      emailConfirmed = false;
      pendingEmailValue = null;
      emailChangeModal?.hide();
    });

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

  // 8. Razorpay Payment & Form Submission
  if (confirmSubmitBtn) {
    confirmSubmitBtn.addEventListener("click", function () {
      const currentEmail = bookingEmailInput.value.trim();
      if (emailNeedsConfirmation(currentEmail)) {
        askAboutEmailChange(currentEmail);
        return;
      }

      if (!agreeTermsCheckbox.checked) {
        agreeErrorMsg.classList.remove("d-none");
        return;
      }

      const formData = new FormData(form);
      const amount = document.getElementById("estimatedAmount").value;

      if (!amount || parseFloat(amount) <= 0) {
        alert(
          "Please set your property location above so we can calculate the charge.",
        );
        return;
      }

      function resetConfirmBtn() {
        confirmSubmitBtn.disabled = false;
        confirmSubmitBtn.textContent = "Pay & Confirm";
      }

      confirmSubmitBtn.disabled = true;
      confirmSubmitBtn.textContent = "Processing...";

      fetch("assets/api/create-order.php", {
        method: "POST",
        body: new URLSearchParams({ amount: amount }),
      })
        .then((res) => res.json())
        .then((orderRes) => {
          if (!orderRes.status) {
            alert(orderRes.message || "Could not start payment.");
            resetConfirmBtn();
            return;
          }

          const options = {
            key: orderRes.key_id,
            amount: orderRes.amount,
            currency: orderRes.currency,
            name: "Vastu Shakti Rahasya",
            description: "Consultation Reservation",
            order_id: orderRes.order_id,
            prefill: {
              name: document.querySelector('[name="name"]').value,
              email: document.querySelector('[name="email"]').value,
              contact: document.querySelector('[name="mobile"]').value,
            },
            theme: { color: "#C6A87C" },
            modal: { ondismiss: resetConfirmBtn },
            handler: function (rzpResponse) {
              formData.append(
                "razorpay_order_id",
                rzpResponse.razorpay_order_id,
              );
              formData.append(
                "razorpay_payment_id",
                rzpResponse.razorpay_payment_id,
              );
              formData.append(
                "razorpay_signature",
                rzpResponse.razorpay_signature,
              );

              fetch("assets/api/booking.php", {
                method: "POST",
                body: formData,
              })
                .then((response) => response.json())
                .then((data) => {
                  if (data.status) {
                    bootstrap.Modal.getInstance(
                      document.getElementById("confirmBookingModal"),
                    ).hide();
                    const successModal = new bootstrap.Modal(
                      document.getElementById("successModal"),
                    );
                    successModal.show();
                    form.reset();
                  } else {
                    alert(
                      (data.message || "Booking could not be saved.") +
                        "\nYour payment ID: " +
                        rzpResponse.razorpay_payment_id,
                    );
                  }
                })
                .catch((error) => {
                  console.error(error);
                  alert(
                    "Something went wrong saving your booking after payment.\nYour payment ID: " +
                      rzpResponse.razorpay_payment_id,
                  );
                })
                .finally(resetConfirmBtn);
            },
          };

          const rzp = new Razorpay(options);
          rzp.on("payment.failed", function (resp) {
            alert("Payment failed: " + resp.error.description);
            resetConfirmBtn();
          });
          rzp.open();
        })
        .catch((error) => {
          console.error(error);
          alert("Could not start payment. Please try again.");
          resetConfirmBtn();
        });
    });
  }
});

// 9. VastuAura Leaflet Map Location + Distance-based Pricing
(function () {
  "use strict";

  const SHOP_LOCATION = {
    lat: 18.516583,
    lng: 73.853917,
    label: "Vastu Shakti Rahasya, Appa Balwant Chowk, Pune",
  };

  const PRICE_CONFIG = {
    currency: "₹",
    distanceThresholdKm: 10,
    flat: {
      "1bhk": { withinRadius: 20000, beyondRadius: 30000 },
      "2bhk": { withinRadius: 20000, beyondRadius: 30000 },
      "3bhk": { withinRadius: 25000, beyondRadius: 35000 },
    },
    commercial: {
      baseSqft: 1000,
      baseAmount: 20000,
      stepSqft: 500,
      stepAmount: 5000,
    },
  };

  let lastDistanceKm = null;
  let map, shopMarker, clientMarker;
  let currentClientLatLng = null;

  function haversineKm(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = ((lat2 - lat1) * Math.PI) / 180;
    const dLon = ((lon2 - lon1) * Math.PI) / 180;
    const a =
      Math.sin(dLat / 2) ** 2 +
      Math.cos((lat1 * Math.PI) / 180) *
        Math.cos((lat2 * Math.PI) / 180) *
        Math.sin(dLon / 2) ** 2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  }

  function computeAmount(distanceKm) {
    const propertyType = document.querySelector('[name="property_type"]').value;
    if (propertyType === "Commercial") {
      const sqft = parseFloat(document.getElementById("sqftInput").value) || 0;
      const cfg = PRICE_CONFIG.commercial;
      if (!sqft || sqft <= cfg.baseSqft) return cfg.baseAmount;
      const extraSqft = sqft - cfg.baseSqft;
      return (
        cfg.baseAmount + Math.ceil(extraSqft / cfg.stepSqft) * cfg.stepAmount
      );
    }
    const bhk = document.querySelector('[name="bhk_type"]').value;
    const tier = PRICE_CONFIG.flat[bhk] || PRICE_CONFIG.flat["1bhk"];
    return distanceKm <= PRICE_CONFIG.distanceThresholdKm
      ? tier.withinRadius
      : tier.beyondRadius;
  }

  function setLoadingState(isLoading) {
    if (isLoading) {
      document.getElementById("distanceValue").textContent = "Calculating...";
      document.getElementById("amountValue").textContent = "Calculating...";
    }
  }

  async function fetchDrivingDistanceKm(lat, lng) {
    const url = `https://router.project-osrm.org/route/v1/driving/${SHOP_LOCATION.lng},${SHOP_LOCATION.lat};${lng},${lat}?overview=false`;
    try {
      const res = await fetch(url);
      if (!res.ok) throw new Error("OSRM request failed");
      const data = await res.json();
      if (data.routes && data.routes.length > 0)
        return data.routes[0].distance / 1000;
      throw new Error("No route found");
    } catch (err) {
      return haversineKm(SHOP_LOCATION.lat, SHOP_LOCATION.lng, lat, lng);
    }
  }

  async function updateDistanceAndPrice(lat, lng) {
    setLoadingState(true);
    const distanceKm = await fetchDrivingDistanceKm(lat, lng);
    lastDistanceKm = distanceKm;
    document.getElementById("distanceValue").textContent =
      distanceKm.toFixed(2) + " km";
    document.getElementById("clientLat").value = lat.toFixed(6);
    document.getElementById("clientLng").value = lng.toFixed(6);
    document.getElementById("distanceKm").value = distanceKm.toFixed(2);
    recalcAndDisplayAmount();
  }

  function recalcAndDisplayAmount() {
    if (lastDistanceKm === null) return;
    const amount = computeAmount(lastDistanceKm);
    document.getElementById("amountValue").textContent =
      PRICE_CONFIG.currency + amount.toLocaleString("en-IN");
    document.getElementById("estimatedAmount").value = amount;
  }

  async function reverseGeocode(lat, lng) {
    try {
      const res = await fetch(
        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`,
        { headers: { Accept: "application/json" } },
      );
      const data = await res.json();
      return data.display_name || "";
    } catch (err) {
      return "";
    }
  }

  function setClientLocation(lat, lng, { updateAddressBox = true } = {}) {
    currentClientLatLng = { lat, lng };
    clientMarker.setLatLng([lat, lng]);
    map.setView([lat, lng], 14);
    updateDistanceAndPrice(lat, lng);
    if (updateAddressBox) {
      reverseGeocode(lat, lng).then((addr) => {
        if (addr) document.getElementById("bookingAddress").value = addr;
      });
    }
  }

  function initMap() {
    map = L.map("vastuMap").setView([SHOP_LOCATION.lat, SHOP_LOCATION.lng], 12);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors",
      maxZoom: 19,
    }).addTo(map);
    const shopIcon = L.divIcon({
      className: "shop-marker",
      html: "🏠",
      iconSize: [30, 30],
    });
    shopMarker = L.marker([SHOP_LOCATION.lat, SHOP_LOCATION.lng], {
      icon: shopIcon,
      draggable: false,
    })
      .addTo(map)
      .bindPopup(SHOP_LOCATION.label);
    clientMarker = L.marker([SHOP_LOCATION.lat, SHOP_LOCATION.lng], {
      draggable: true,
    })
      .addTo(map)
      .bindPopup("Your location (drag to adjust)");
    clientMarker.on("dragend", function (e) {
      setClientLocation(e.target.getLatLng().lat, e.target.getLatLng().lng);
    });
    map.on("click", function (e) {
      setClientLocation(e.latlng.lat, e.latlng.lng);
    });
  }

  function initControls() {
    const useMyLocationBtn = document.getElementById("useMyLocationBtn");
    const searchInput = document.getElementById("locationSearchInput");
    const searchBtn = document.getElementById("locationSearchBtn");

    useMyLocationBtn.addEventListener("click", function () {
      if (!navigator.geolocation)
        return alert("Geolocation is not supported by your browser.");
      useMyLocationBtn.disabled = true;
      useMyLocationBtn.textContent = "Locating...";
      navigator.geolocation.getCurrentPosition(
        function (pos) {
          setClientLocation(pos.coords.latitude, pos.coords.longitude);
          useMyLocationBtn.disabled = false;
          useMyLocationBtn.textContent = "📍 Use My Current Location";
        },
        function (err) {
          alert("Couldn't fetch your location. You can search or use the map.");
          useMyLocationBtn.disabled = false;
          useMyLocationBtn.textContent = "📍 Use My Current Location";
        },
        { enableHighAccuracy: true, timeout: 10000 },
      );
    });

    const PUNE_VIEWBOX = "73.74,18.63,73.98,18.42";
    async function geocode(query) {
      const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=in&viewbox=${PUNE_VIEWBOX}&bounded=0&limit=5&addressdetails=1`;
      const res = await fetch(url, { headers: { Accept: "application/json" } });
      if (!res.ok) throw new Error(`Nominatim HTTP ${res.status}`);
      return res.json();
    }

    function renderSuggestions(results) {
      let box = document.getElementById("locationSuggestions");
      if (!box) {
        box = document.createElement("div");
        box.id = "locationSuggestions";
        box.className = "list-group";
        searchInput.parentElement.appendChild(box);
      }
      box.innerHTML = "";
      if (results.length === 0) {
        box.innerHTML =
          '<div class="list-group-item text-muted small">No matches found. Try dragging the pin.</div>';
        return;
      }
      results.forEach((r) => {
        const item = document.createElement("button");
        item.type = "button";
        item.className = "list-group-item list-group-item-action small";
        item.textContent = r.display_name;
        item.addEventListener("click", function () {
          setClientLocation(parseFloat(r.lat), parseFloat(r.lon), {
            updateAddressBox: false,
          });
          document.getElementById("bookingAddress").value = r.display_name;
          box.innerHTML = "";
          searchInput.value = r.display_name;
        });
        box.appendChild(item);
      });
    }

    async function performSearch(query) {
      let results = await geocode(query);
      if (results.length === 0 && !/pune/i.test(query))
        results = await geocode(`${query}, Pune`);
      return results;
    }

    async function runSearch() {
      const query = searchInput.value.trim();
      if (!query) return;
      searchBtn.disabled = true;
      searchBtn.textContent = "Searching...";
      try {
        const results = await performSearch(query);
        renderSuggestions(results);
        if (results.length === 1)
          setClientLocation(
            parseFloat(results[0].lat),
            parseFloat(results[0].lon),
            { updateAddressBox: false },
          );
      } catch (err) {
        alert(
          "Search failed. You can still set the location by dragging the pin.",
        );
      } finally {
        searchBtn.disabled = false;
        searchBtn.textContent = "Search";
      }
    }

    searchBtn.addEventListener("click", runSearch);
    searchInput.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        clearTimeout(debounceTimer);
        runSearch();
      }
    });

    let debounceTimer = null;
    searchInput.addEventListener("input", function () {
      const query = searchInput.value.trim();
      clearTimeout(debounceTimer);
      if (query.length < 3) {
        const box = document.getElementById("locationSuggestions");
        if (box) box.innerHTML = "";
        return;
      }
      debounceTimer = setTimeout(async function () {
        try {
          renderSuggestions(await performSearch(query));
        } catch (err) {}
      }, 350);
    });

    document.addEventListener("click", function (e) {
      const box = document.getElementById("locationSuggestions");
      if (!box || e.target === searchInput || box.contains(e.target)) return;
      box.innerHTML = "";
    });
  }

  function initPropertyFields() {
    const propertyTypeSelect = document.getElementById("propertyType");
    const bhkField = document.getElementById("bhkField");
    const bhkSelect = document.getElementById("bhkType");
    const sqftField = document.getElementById("sqftField");
    const sqftInput = document.getElementById("sqftInput");

    function toggleFields() {
      if (propertyTypeSelect.value === "Commercial") {
        bhkField.classList.add("d-none");
        sqftField.classList.remove("d-none");
        bhkSelect.required = false;
        sqftInput.required = true;
      } else {
        sqftField.classList.add("d-none");
        bhkField.classList.remove("d-none");
        sqftInput.required = false;
        bhkSelect.required = true;
      }
      recalcAndDisplayAmount();
    }
    propertyTypeSelect.addEventListener("change", toggleFields);
    bhkSelect.addEventListener("change", recalcAndDisplayAmount);
    sqftInput.addEventListener("input", recalcAndDisplayAmount);
    toggleFields();
  }

  document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("vastuMap")) {
      initMap();
      initControls();
      initPropertyFields();
      updateDistanceAndPrice(SHOP_LOCATION.lat, SHOP_LOCATION.lng);
    }
  });

  window.VastuLocation = {
    getShopLocation: () => SHOP_LOCATION,
    getClientLocation: () => currentClientLatLng,
    getPriceConfig: () => PRICE_CONFIG,
  };
})();
