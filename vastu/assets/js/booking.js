const form = document.getElementById("bookingForm");
const openModalBtn = document.getElementById("openConfirmModal");

openModalBtn.addEventListener("click", function () {

    const timeSelect = document.getElementById("preferred_time");
    const noSlotsMsg = document.getElementById("noSlotsMsg");

    // Explicit check: preferred_time must not be empty or disabled
    if (!timeSelect.value || timeSelect.disabled) {
        noSlotsMsg.classList.remove("d-none");
        timeSelect.focus();
        return;
    }

    // Trigger HTML5 validation for everything else
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Fill confirmation modal
    document.getElementById('confirmName').innerText =
        document.querySelector('[name="name"]').value;

    document.getElementById('confirmEmail').innerText =
        document.querySelector('[name="email"]').value;

    document.getElementById('confirmMobile').innerText =
        document.querySelector('[name="mobile"]').value;

    document.getElementById('confirmDate').innerText =
        document.querySelector('[name="preferred_date"]').value;

    document.getElementById('confirmTime').innerText =
        document.querySelector('[name="preferred_time"]').value;

    document.getElementById('confirmType').innerText =
        document.querySelector('[name="consultation_type"]').value;

    document.getElementById('confirmAddress').innerText =
        document.querySelector('[name="address"]').value;

    document.getElementById('confirmUnit').innerText =
        document.querySelector('[name="unit_number"]').value || '—';

    const propType = document.querySelector('[name="property_type"]').value;
    let propertyDetailsText;
    if (propType === 'Commercial') {
        const sqft = document.getElementById('sqftInput').value || '?';
        propertyDetailsText = 'Commercial — ' + sqft + ' sq.ft';
    } else {
        const bhkSelect = document.getElementById('bhkType');
        propertyDetailsText = 'Flat — ' + bhkSelect.options[bhkSelect.selectedIndex].text;
    }
    document.getElementById('confirmPropertyDetails').innerText = propertyDetailsText;

    document.getElementById('confirmDistance').innerText =
        document.getElementById('distanceValue').textContent;

        let amountCalculation = document.getElementById('amountValue').textContent.trim();

        let amount = parseFloat(amountCalculation.replace(/[₹,]/g, ''));

        let halfAmount = amount / 2;

document.getElementById('confirmAmount').innerText =`₹${halfAmount.toLocaleString('en-IN')}`;

    const modal = new bootstrap.Modal(document.getElementById('confirmBookingModal'));
    modal.show();
});


/* =========================================================================
   Razorpay payment + booking submission
   -------------------------------------------------------------------------
   Flow: Confirm Booking clicked
     -> ask our server to create a Razorpay order for the estimated amount
     -> open Razorpay Checkout
     -> on success, send the booking form + Razorpay's payment_id/order_id/
        signature to booking.php, which verifies the signature server-side
        before saving anything.
   ========================================================================= */

document.getElementById("confirmSubmit").addEventListener("click", function () {

  if (!agreeTermsCheckbox.checked) {
    agreeErrorMsg.classList.remove("d-none");
    return; // Razorpay never opens
  }

  const form = document.getElementById("bookingForm");
  const formData = new FormData(form);
  const amount = document.getElementById("estimatedAmount").value;
  const confirmBtn = document.getElementById("confirmSubmit");
  if (!amount || parseFloat(amount) <= 0) {
    alert("Please set your property location above so we can calculate the charge.");
    return;
  }
  function resetConfirmBtn() {
    confirmBtn.disabled = false;
    confirmBtn.textContent = "Confirm Booking";
  }

  confirmBtn.disabled = true;
  confirmBtn.textContent = "Processing...";

  // Step 1: create the Razorpay order server-side
  fetch("assets/api/create-order.php", {
    method: "POST",
    body: new URLSearchParams({ amount: amount })
  })
    .then((res) => res.json())
    .then((orderRes) => {
      if (!orderRes.status) {
        alert(orderRes.message || "Could not start payment.");
        resetConfirmBtn();
        return;
      }

      // Step 2: open Razorpay Checkout
      const options = {
        key: orderRes.key_id,
        amount: orderRes.amount,
        currency: orderRes.currency,
        name: "VastuAura",
        description: "Consultation Booking",
        order_id: orderRes.order_id,
        prefill: {
          name: document.querySelector('[name="name"]').value,
          email: document.querySelector('[name="email"]').value,
          contact: document.querySelector('[name="mobile"]').value
        },
        theme: { color: "#8a6d3b" },
        modal: {
          ondismiss: resetConfirmBtn
        },
        handler: function (rzpResponse) {
          // Step 3: payment succeeded on Razorpay's side - now submit the
          // booking + payment proof to our server for verification & save
          formData.append("razorpay_order_id", rzpResponse.razorpay_order_id);
          formData.append("razorpay_payment_id", rzpResponse.razorpay_payment_id);
          formData.append("razorpay_signature", rzpResponse.razorpay_signature);

          fetch("assets/api/booking.php", {
            method: "POST",
            body: formData
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.status) {
                bootstrap.Modal.getInstance(document.getElementById("confirmBookingModal")).hide();
                const successModal = new bootstrap.Modal(document.getElementById("successModal"));
                successModal.show();
                form.reset();
              } else {
                // Payment went through on Razorpay's side but saving failed -
                // surface the payment_id so support can reconcile it manually.
                alert(
                  (data.message || "Booking could not be saved.") +
                  "\nYour payment ID (please save this): " + rzpResponse.razorpay_payment_id
                );
              }
            })
            .catch((error) => {
              console.error(error);
              alert(
                "Something went wrong saving your booking after payment.\n" +
                "Your payment ID (please save this): " + rzpResponse.razorpay_payment_id
              );
            })
            .finally(resetConfirmBtn);
        }
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


/* =========================================================================
   VastuAura - Location + Distance-based Pricing
   -------------------------------------------------------------------------
   - Uses Leaflet.js + OpenStreetMap tiles (free, no API key required)
   - Fixed origin point: Shop @ Appa Balwant Chowk, Pune
   - Client can: allow browser geolocation, drag the marker, click the map,
     or search an address (via OSM Nominatim) to set their location
   - Distance is fetched from OSRM's free public routing server (driving
     distance). If that call fails (offline/blocked), it falls back to a
     straight-line (Haversine) distance so the form never breaks.
   - Final amount = baseFee + perKmRate * billableKm, billableKm = distance
     beyond the free minDistanceKm radius.
   ========================================================================= */

(function () {
  "use strict";

  // ---------------------------------------------------------------------
  // 1. CONFIG — edit these two blocks to match your business rules
  // ---------------------------------------------------------------------
  
  const SHOP_LOCATION = {
    lat: 18.516583,
    lng: 73.853917,
    label: "VastuAura Shop, Appa Balwant Chowk, Pune"
  };

  const PRICE_CONFIG = {
    currency: "₹",

    // Distance cutoff that decides which flat rate applies
    distanceThresholdKm: 10,

    // Flat / residential pricing: fixed amount by BHK, split by distance
    flat: {
      "1bhk": { withinRadius: 20000, beyondRadius: 30000 },
      "2bhk": { withinRadius: 20000, beyondRadius: 30000 },
      "3bhk": { withinRadius: 25000, beyondRadius: 35000 }
    },

    // Commercial pricing: base amount up to baseSqft, then +stepAmount for
    // every additional stepSqft (e.g. 1000 sqft -> 20,000; 1500 -> 25,000;
    // 2000 -> 30,000; ...)
    commercial: {
      baseSqft: 1000,
      baseAmount: 20000,
      stepSqft: 500,
      stepAmount: 5000
    }
  };

  // ---------------------------------------------------------------------
  // 1b. STATE for the last-known distance, so changing property/BHK/sqft
  //     recalculates instantly without re-hitting the routing API
  // ---------------------------------------------------------------------
  let lastDistanceKm = null;

  // ---------------------------------------------------------------------
  // 2. STATE
  // ---------------------------------------------------------------------
  let map, shopMarker, clientMarker;
  let currentClientLatLng = null;

  // ---------------------------------------------------------------------
  // 3. HELPERS
  // ---------------------------------------------------------------------

  // Haversine straight-line distance (km) — used only as a fallback
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

  function computeFlatAmount(bhk, distanceKm) {
    const tier = PRICE_CONFIG.flat[bhk] || PRICE_CONFIG.flat["1bhk"];
    return distanceKm <= PRICE_CONFIG.distanceThresholdKm
      ? tier.withinRadius
      : tier.beyondRadius;
  }

  function computeCommercialAmount(sqft) {
    const cfg = PRICE_CONFIG.commercial;
    if (!sqft || sqft <= cfg.baseSqft) return cfg.baseAmount;
    const extraSqft = sqft - cfg.baseSqft;
    const extraSteps = Math.ceil(extraSqft / cfg.stepSqft);
    return cfg.baseAmount + extraSteps * cfg.stepAmount;
  }

  function computeAmount(distanceKm) {
    const propertyType = document.querySelector('[name="property_type"]').value;
    if (propertyType === "Commercial") {
      const sqft = parseFloat(document.getElementById("sqftInput").value) || 0;
      return computeCommercialAmount(sqft);
    }
    const bhk = document.querySelector('[name="bhk_type"]').value;
    return computeFlatAmount(bhk, distanceKm);
  }

  function setLoadingState(isLoading) {
    const distEl = document.getElementById("distanceValue");
    const amtEl = document.getElementById("amountValue");
    if (isLoading) {
      distEl.textContent = "Calculating...";
      amtEl.textContent = "Calculating...";
    }
  }

  // Try OSRM driving distance first, fall back to straight-line
  async function fetchDrivingDistanceKm(lat, lng) {
    const url = `https://router.project-osrm.org/route/v1/driving/${SHOP_LOCATION.lng},${SHOP_LOCATION.lat};${lng},${lat}?overview=false`;
    try {
      const res = await fetch(url);
      if (!res.ok) throw new Error("OSRM request failed");
      const data = await res.json();
      if (data.routes && data.routes.length > 0) {
        return data.routes[0].distance / 1000; // meters -> km
      }
      throw new Error("No route found");
    } catch (err) {
      console.warn("OSRM unavailable, using straight-line distance:", err);
      return haversineKm(SHOP_LOCATION.lat, SHOP_LOCATION.lng, lat, lng);
    }
  }

  async function updateDistanceAndPrice(lat, lng) {
    setLoadingState(true);
    const distanceKm = await fetchDrivingDistanceKm(lat, lng);
    lastDistanceKm = distanceKm;

    document.getElementById("distanceValue").textContent =
      distanceKm.toFixed(2) + " km";

    // Hidden fields submitted with the booking form
    document.getElementById("clientLat").value = lat.toFixed(6);
    document.getElementById("clientLng").value = lng.toFixed(6);
    document.getElementById("distanceKm").value = distanceKm.toFixed(2);

    recalcAndDisplayAmount();
  }

  // Recomputes and displays the amount using the last-known distance —
  // called whenever property type / BHK / sq.ft changes, with no need to
  // re-fetch the driving distance each time.
  function recalcAndDisplayAmount() {
    if (lastDistanceKm === null) return;
    const amount = computeAmount(lastDistanceKm);
    document.getElementById("amountValue").textContent =
      PRICE_CONFIG.currency + amount.toLocaleString("en-IN");
    document.getElementById("estimatedAmount").value = amount;
  }

  // Reverse geocode (lat/lng -> readable address) using Nominatim
  async function reverseGeocode(lat, lng) {
    try {
      const res = await fetch(
        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`,
        { headers: { Accept: "application/json" } }
      );
      const data = await res.json();
      return data.display_name || "";
    } catch (err) {
      console.warn("Reverse geocode failed:", err);
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

  // ---------------------------------------------------------------------
  // 4. MAP INITIALIZATION
  // ---------------------------------------------------------------------
  function initMap() {
    map = L.map("vastuMap").setView([SHOP_LOCATION.lat, SHOP_LOCATION.lng], 12);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors",
      maxZoom: 19
    }).addTo(map);

    // Fixed shop marker
    const shopIcon = L.divIcon({
      className: "shop-marker",
      html: "🏠",
      iconSize: [30, 30]
    });
    shopMarker = L.marker([SHOP_LOCATION.lat, SHOP_LOCATION.lng], {
      icon: shopIcon,
      draggable: false
    })
      .addTo(map)
      .bindPopup(SHOP_LOCATION.label);

    // Draggable client marker, starts at shop until user sets a location
    clientMarker = L.marker([SHOP_LOCATION.lat, SHOP_LOCATION.lng], {
      draggable: true
    })
      .addTo(map)
      .bindPopup("Your location (drag to adjust)");

    clientMarker.on("dragend", function (e) {
      const { lat, lng } = e.target.getLatLng();
      setClientLocation(lat, lng);
    });

    // Click anywhere on the map to set the client location
    map.on("click", function (e) {
      setClientLocation(e.latlng.lat, e.latlng.lng);
    });
  }

  // ---------------------------------------------------------------------
  // 5. UI CONTROLS: geolocation button + address search
  // ---------------------------------------------------------------------
  function initControls() {
    const useMyLocationBtn = document.getElementById("useMyLocationBtn");
    const searchInput = document.getElementById("locationSearchInput");
    const searchBtn = document.getElementById("locationSearchBtn");

    useMyLocationBtn.addEventListener("click", function () {
      if (!navigator.geolocation) {
        alert("Geolocation is not supported by your browser. Please set your location on the map instead.");
        return;
      }
      useMyLocationBtn.disabled = true;
      useMyLocationBtn.textContent = "Locating...";

      navigator.geolocation.getCurrentPosition(
        function (pos) {
          setClientLocation(pos.coords.latitude, pos.coords.longitude);
          useMyLocationBtn.disabled = false;
          useMyLocationBtn.textContent = "📍 Use My Current Location";
        },
        function (err) {
          console.warn("Geolocation error:", err);
          alert("Couldn't fetch your location. You can search an address or click/drag the marker on the map.");
          useMyLocationBtn.disabled = false;
          useMyLocationBtn.textContent = "📍 Use My Current Location";
        },
        { enableHighAccuracy: true, timeout: 10000 }
      );
    });

    // Rough bounding box around Pune city to bias/limit results to the area
    // (left,top,right,bottom = west,north,east,south)
    const PUNE_VIEWBOX = "73.74,18.63,73.98,18.42";

    async function geocode(query) {
      const url =
        `https://nominatim.openstreetmap.org/search?format=json` +
        `&q=${encodeURIComponent(query)}` +
        `&countrycodes=in` +
        `&viewbox=${PUNE_VIEWBOX}&bounded=0` + // bounded=0: prefer, don't strictly restrict
        `&limit=5&addressdetails=1`;

      const res = await fetch(url, {
        headers: { Accept: "application/json" }
      });

      if (!res.ok) {
        const bodyText = await res.text().catch(() => "");
        console.error(
          `Nominatim search failed: HTTP ${res.status} ${res.statusText}`,
          bodyText.slice(0, 300)
        );
        throw new Error(`Nominatim HTTP ${res.status}`);
      }

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
          '<div class="list-group-item text-muted small">No matches found. Try a nearby landmark name, or just click/drag the pin on the map to set your location manually.</div>';
        return;
      }

      results.forEach((r) => {
        const item = document.createElement("button");
        item.type = "button";
        item.className = "list-group-item list-group-item-action small";
        item.textContent = r.display_name;
        item.addEventListener("click", function () {
          setClientLocation(parseFloat(r.lat), parseFloat(r.lon), {
            updateAddressBox: false
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
      if (results.length === 0 && !/pune/i.test(query)) {
        results = await geocode(`${query}, Pune`);
      }
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
        if (results.length === 1) {
          setClientLocation(parseFloat(results[0].lat), parseFloat(results[0].lon), {
            updateAddressBox: false
          });
        }
      } catch (err) {
        console.error("Address search failed:", err);
        alert(
          "Search failed (" + err.message + "). Open your browser console (F12) for details — " +
          "this usually means the request to nominatim.openstreetmap.org is being blocked " +
          "(ad-blocker, corporate/network firewall, or no internet from this device). " +
          "You can still set the location by clicking/dragging the pin on the map."
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
          const results = await performSearch(query);
          renderSuggestions(results);
        } catch (err) {
          console.warn("Live suggestion search failed:", err);
        }
      }, 350);
    });

    document.addEventListener("click", function (e) {
      const box = document.getElementById("locationSuggestions");
      if (!box) return;
      if (e.target === searchInput || box.contains(e.target)) return;
      box.innerHTML = "";
    });
  }

  // ---------------------------------------------------------------------
  // 5b. PROPERTY TYPE / BHK / SQ.FT — drives the pricing calculation
  // ---------------------------------------------------------------------
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

  // ---------------------------------------------------------------------
  // 6. INIT ON PAGE LOAD
  // ---------------------------------------------------------------------
  document.addEventListener("DOMContentLoaded", function () {
    initMap();
    initControls();
    initPropertyFields();
    updateDistanceAndPrice(SHOP_LOCATION.lat, SHOP_LOCATION.lng);
  });

  window.VastuLocation = {
    getShopLocation: () => SHOP_LOCATION,
    getClientLocation: () => currentClientLatLng,
    getPriceConfig: () => PRICE_CONFIG
  };
})();


// Terms & Conditions gate for the Confirm Booking button
const agreeTermsCheckbox = document.getElementById("agreeTerms");
const agreeErrorMsg = document.getElementById("agreeError");
const confirmSubmitBtn = document.getElementById("confirmSubmit");

agreeTermsCheckbox.addEventListener("change", function () {
    confirmSubmitBtn.disabled = !this.checked;
    if (this.checked) {
        agreeErrorMsg.classList.add("d-none");
    }
});

// Reset the checkbox + button state each time the modal reopens,
// so a re-edited booking doesn't silently carry over a stale "agreed" state
document.getElementById('confirmBookingModal').addEventListener('show.bs.modal', function () {
    agreeTermsCheckbox.checked = false;
    confirmSubmitBtn.disabled = true;
    agreeErrorMsg.classList.add("d-none");
});

function loadSlotsForDate(selectedDate) {
  const dropdown = document.getElementById('preferred_time');
  const noSlotsMsg = document.getElementById('noSlotsMsg');

  dropdown.innerHTML = '<option disabled selected value="">-- Select Preferred Time --</option>';
  dropdown.disabled = false;
  noSlotsMsg.classList.add('d-none');

  fetch(`assets/api/booking.php?action=get_booking_slots&date=${encodeURIComponent(selectedDate)}`)
      .then(response => response.json())
      .then(result => {
          if (result.status === 'success') {
              if (result.data.length === 0) {
                  dropdown.disabled = true;
                  noSlotsMsg.classList.remove('d-none');
                  return;
              }
              result.data.forEach((slot) => {
                  const option = document.createElement('option');
                  option.value = slot.value;
                  option.textContent = slot.value;
                  dropdown.appendChild(option);
              });
              dropdown.selectedIndex = 1; // auto-select first available slot
          }
      })
      .catch(error => console.error('Error loading booking slots:', error));
}

document.addEventListener('DOMContentLoaded', function () {

  const dateInput = document.getElementById('bookingDate');

  // Tomorrow's date (min + default)
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  const yyyy = tomorrow.getFullYear();
  const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
  const dd = String(tomorrow.getDate()).padStart(2, '0');
  const tomorrowStr = `${yyyy}-${mm}-${dd}`;

  dateInput.min = tomorrowStr;
  dateInput.value = tomorrowStr;

  // Load slots for the default date on page load
  loadSlotsForDate(tomorrowStr);

  // Reload slots every time the user picks a different date
  dateInput.addEventListener('change', function () {
      if (this.value) {
          loadSlotsForDate(this.value);
      }
  });
});