document.getElementById("checkLogout").addEventListener("click", function(e) {
  e.preventDefault();

  let confirmLogout = confirm("Are you sure you want to logout?");

  if (confirmLogout) {
      window.location.href = "assets/api/logout.php";
  }
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


// Confirm Order

document.getElementById("confirmOrder")
.addEventListener("click",()=>{


    // Hide confirmation

    document.getElementById("confirmModal")
    .style.display="none";



    const payment =
    document.querySelector("select").value;



    fetch("assets/api/order.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify({

            payment_method:payment

        })

    })


    .then(res=>res.json())


    .then(res=>{


        if(res.success){


            // Show success modal

            document.getElementById("successModal")
            .style.display="flex";


            // Store order id for invoice button

            document.getElementById("invoice")
            .onclick=function(){

                window.location =
                "invoice.php?order_id="+res.order_id;

            }



        }
        else{


            alert(res.message);


        }


    })

    .catch(error=>{


        console.log(error);

        alert("Something went wrong");


    });



});
