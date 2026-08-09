

document.addEventListener("DOMContentLoaded", loadCart);

function loadCart() {

    fetch("assets/api/cart.php")
    .then(res => res.json())
    .then(res => {

        if (!res.success) return;

        // Cart is empty
        if (res.data.length === 0) {

            document.getElementById("cartItems").innerHTML = `
                <div class="text-center py-5">
                    <h3>Your cart is empty 🛒</h3>
                    <p class="text-muted">
                        Looks like you haven't added any Vastu products yet.
                    </p>

                    <a href="store.php" class="btn btn-brand mt-3">
                        Continue Shopping
                    </a>
                </div>
            `;

            document.getElementById("subtotalValue").innerHTML = "₹0";
            document.getElementById("totalValue").innerHTML = "₹0";

            return;
        }

        let html = "";
        let subtotal = 0;

        res.data.forEach(item => {

            subtotal += item.price * item.quantity;

            html += `
                <div class="cart-item mb-4">

                    <div class="row align-items-center">

                        <div class="col-md-2">
                            <img
                                src="uploads/products/${item.image}"
                                class="img-fluid rounded">
                        </div>

                        <div class="col-md-4">
                            <h5>${item.name}</h5>
                            <p>₹${item.price}</p>
                        </div>

                        <div class="col-md-2">
                            Qty : ${item.quantity}
                        </div>

                        <div class="col-md-2">
                            ₹${item.price * item.quantity}
                        </div>

                        <div class="col-md-2">
                        <button class="btn btn-outline-danger btn-sm rounded-pill" onclick="removeCart(${item.cart_id})">
    <i class="fa-solid fa-trash"></i> Remove
</button>
                           
                        </div>

                    </div>

                </div>
            `;
        });

        document.getElementById("cartItems").innerHTML = html;

        document.getElementById("subtotalValue").innerHTML = "₹" + subtotal;
        document.getElementById("totalValue").innerHTML = "₹" + (subtotal + 150);

    });

}


function removeCart(cartId) {

    showConfirmModal(

        "Remove Product",

        "Are you sure you want to remove this product from your cart?",

        function () {

            fetch("assets/api/remove-cart.php", {

                method: "POST",

                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },

                body: "cart_id=" + cartId

            })
            .then(res => res.json())
            .then(res => {

                if (res.success) {
                    loadCart();
                    const modal = bootstrap.Modal.getOrCreateInstance(
                        document.getElementById("commonModal")
                    );
                    modal.hide();
                    

                } else {

                    alert(res.message);

                }

            });

        }

    );

}


document.getElementById("checkoutBtn").addEventListener("click", function (e) {

    // Get cart from localStorage
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    if (cart.length === 0) {
        
    }

    fetch("assets/api/cart.php")
    .then(res => res.json())
    .then(res => {

        if (!res.success) return;

        // Cart is empty
        if (res.data.length === 0) {

            e.preventDefault();

            const modal = new bootstrap.Modal(
                document.getElementById("emptyCartModal")
            );
    
            modal.show();
        
        }else {
            // Cart has products
            window.location.href = "checkout.php";
        }

       
    });

   
});
