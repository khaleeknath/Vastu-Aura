document.getElementById("myOrderLogout").addEventListener("click", function(e) {
    e.preventDefault();
  
    let confirmLogout = confirm("Are you sure you want to logout?");
  
    if (confirmLogout) {
        window.location.href = "assets/api/logout.php";
    }
  });


document.addEventListener("DOMContentLoaded", () => {

    loadOrders();

});

function loadOrders() {

    fetch("assets/api/myorder.php")

    .then(response => response.json())

    .then(response => {

        const container = document.getElementById("ordersContainer");

        if (!response.success) {

            container.innerHTML = `

                <div class="alert alert-danger">

                    ${response.message}

                </div>

            `;

            return;
        }

        if (response.orders.length === 0) {

            container.innerHTML = `

                <div class="text-center py-5">

                    <h2>🛒</h2>
                    <h3 class="mt-4">

                        No Orders Found.

                    </h3>

                    <p>

                        Looks like you haven't placed any orders yet.

                    </p>

                    <a href="store.php"
                       class="btn btn-brand">

                        Continue Shopping

                    </a>

                </div>

            `;

            return;

        }

        let html = "";

        response.orders.forEach(order => {

            let badge = "";

            switch(order.order_status.toLowerCase()){

                case "pending":

                    badge = "bg-warning text-dark";

                    break;

                case "confirmed":

                    badge = "bg-primary";

                    break;

                case "processing":

                    badge = "bg-info text-dark";

                    break;

                case "shipped":

                    badge = "bg-secondary";

                    break;

                case "delivered":

                    badge = "bg-success";

                    break;

                case "cancelled":

                    badge = "bg-danger";

                    break;

                default:

                    badge = "bg-dark";

            }

            html += `

            <div class="order-card">

                <div class="row align-items-center">

                    <div class="col-lg-8">

                        <div class="d-flex justify-content-between align-items-center">

                            <h4>
            📦 Order #VA-${String(order.id).padStart(6, "0")}
        </h4>
                            <span class="badge ${badge}">

                                ${order.order_status}

                            </span>

                        </div>

                        <hr>

                        <div class="row">

                            <div class="col-md-4">

                                <small>Order Date</small>

                                <h6>

                                    ${(order.order_date)}

                                </h6>

                            </div>

                            <div class="col-md-4">

                                <small>Payment</small>

                                <h6>

                                    ${order.payment_method}

                                </h6>

                            </div>

                            <div class="col-md-4">

                                <small>Total Amount</small>

                                <h5 class="text-brand">

                                    ₹${order.total_amount}

                                </h5>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

                        <a href="order-details.php?order_id=${order.id}"

                           class="btn btn-brand">

                            View Details

                        </a>

                    </div>

                </div>

            </div>

            `;

        });

        container.innerHTML = html;

    })

    .catch(error => {

        console.log(error);

    });

}