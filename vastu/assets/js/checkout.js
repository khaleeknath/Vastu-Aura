document.getElementById("checkLogout").addEventListener("click", function(e) {
  e.preventDefault();

  let confirmLogout = confirm("Are you sure you want to logout?");

  if (confirmLogout) {
      window.location.href = "assets/api/logout.php";
  }
});

document.getElementById('emailChangeConfirm')?.addEventListener('click', () => {
    const newEmail = pendingEmailValue;
    const btn = document.getElementById('emailChangeConfirm');
    btn.disabled = true;
    btn.textContent = 'Updating...';
  
    fetch('assets/api/update-email.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'email=' + encodeURIComponent(newEmail)
    })
      .then(r => r.json())
      .then(data => {
        btn.disabled = false;
        btn.textContent = 'Yes, update it';
        if (data.status) {
          emailConfirmed = true;
          emailChangeModal?.hide();
        } else {
          alert(data.message || 'Could not update email.');
          bookingEmailInput.value = originalEmail;
          emailChangeModal?.hide();
        }
      })
      .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Yes, update it';
        console.log('Network error updating email.');
        bookingEmailInput.value = originalEmail;
        emailChangeModal?.hide();
      });
  });


document.addEventListener("DOMContentLoaded", loadCheckoutSummary);

function loadCheckoutSummary() {

    fetch("assets/api/order-Summary.php")
        .then(res => res.json())
        .then(res => {

            if (!res.success) return;

            let html = "";

            let subtotal = 0;

            res.data.forEach(item => {

                let total = item.price * item.quantity;

                subtotal += total;

                html += `
                    <div class="d-flex justify-content-between border-bottom py-2">

                        <div>
                            <strong>${item.name}</strong><br>
                            Qty : ${item.quantity}
                        </div>

                        <div>
                            ₹${total}
                        </div>

                    </div>
                `;
            });

            document.getElementById("checkoutItems").innerHTML = html;

            document.getElementById("checkoutSubtotal").innerHTML =
                "₹" + subtotal;

            document.getElementById("checkoutTotal").innerHTML =
                "₹" + (subtotal + 150);
    // --- COD availability based on order total ---
            toggleCOD(subtotal + 150);
        });

}

document.getElementById("checkoutForm")
.addEventListener("submit", function(e){

    e.preventDefault();
    // Show confirmation modal

    document.getElementById("confirmModal")
    .style.display = "flex";
});

// Cancel Order

document.getElementById("cancelOrder")
.addEventListener("click",()=>{
    document.getElementById("confirmModal")
    .style.display="none";
});


function toggleCOD(orderTotal) {
    const codOption = document.getElementById("codOption");
    const codHint = document.getElementById("codHint");
    const paymentSelect = document.getElementById("paymentMethod");

    const codAllowed = orderTotal > 3000;

    codOption.disabled = !codAllowed;
    codHint.style.display = codAllowed ? "none" : "block";

    // If COD was pre-selected but is no longer allowed, fall back to Card Payment
    if (!codAllowed && paymentSelect.value === "Cash on Delivery") {
        paymentSelect.value = "Card Payment";
    }
}

// Confirm Order

document.getElementById("confirmOrder").addEventListener("click", async () => {
    document.getElementById("confirmModal").style.display = "none";

    const paymentMethod = document.querySelector("select").value;

    // Cash on Delivery skips Razorpay entirely — unchanged behavior
    if (paymentMethod === "Cash on Delivery") {
        placeOrder({ payment_method: paymentMethod });
        return;
    }

    // Card / UPI — take payment first via Razorpay
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
            name: "VastuAura",
            description: "Store Order",
            prefill: {
                name: document.getElementById("checkoutFirstName").value + " " +
                      document.getElementById("checkoutLastName").value,
                email: document.getElementById("checkoutEmail").value,
                contact: document.getElementById("checkoutPhone").value
            },
            theme: { color: "#8a6d3b" },
            handler: function (response) {
                // Payment succeeded — now confirm + create the order server-side
                placeOrder({
                    email: document.getElementById("checkoutEmail").value ,
                    payment_method: paymentMethod,
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_signature: response.razorpay_signature
                });
            }
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

function placeOrder(payload) {
    fetch("assets/api/Order.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            document.getElementById("successModal").style.display = "flex";
            document.getElementById("invoice").onclick = function () {
                window.location = "invoice.php?order_id=" + res.order_id;
            };
            if (!res.email_sent) {
                // optional: show a small inline note, don't block success
                console.warn("Order placed, but confirmation email failed to send.");
            }
        } else {
            alert(res.message);
        }
    })
    .catch(error => {
        console.log(error);
        alert("Something went wrong");
    });
}
