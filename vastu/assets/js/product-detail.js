document.addEventListener("DOMContentLoaded", function () {


    const addToCartBtn = document.getElementById("detailAddToCart");

    const confirmModal = document.getElementById("productconfirmModal");

    const successModal = document.getElementById("successModal");


    const cancelAction = document.getElementById("cancelOrder");

    const confirmAction = document.getElementById("confirmOrder");

    const successAction = document.getElementById("continueShopping");



    // Open Confirmation Modal

    addToCartBtn.addEventListener("click",()=>{


        confirmModal.style.display="flex";


    });


    // Cancel

    cancelAction.addEventListener("click",()=>{


        confirmModal.style.display="none";


    });

    // Confirm Add To Cart

    confirmAction.addEventListener("click",()=>{


        confirmModal.style.display="none";



        fetch("assets/api/add-to-cart.php", {

            method:"POST",

            headers:{

                "Content-Type":"application/json"

            },


            body:JSON.stringify({

                product_id: currentProduct.data.id,

                quantity: qty

            })

        })


        .then(res=>res.json())


        .then(res=>{


            if(res.success){


                document.querySelector("#successModal h3")
                .innerHTML="Added To Cart!";


                document.querySelector("#successModal p")
                .innerHTML=
                "Your product has been added successfully to your cart.";



                successModal.style.display="flex";


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


    // Success Button

    successAction.addEventListener("click",()=>{


        window.location.href="store.php";


    });



});

document.addEventListener(
    'DOMContentLoaded',
    loadProduct
);

let currentProduct = null;


function loadProduct() {

    const params =
        new URLSearchParams(
            window.location.search
        );

    const productId =
        params.get('id');

    if (!productId) {

        alert('Product ID Missing');

        return;
    }

    fetch(
        `assets/api/productDetail.php?id=${productId}`
    )
        .then(response => response.json())

        .then(result => {

            console.log(result);

            currentProduct = result;
            const product =
                result.data;

            if (!product) {

                alert(
                    'Product Not Found'
                );

                return;
            }

            document.getElementById(
                'productCategory'
            ).innerText =
                product.category_name;

            document.getElementById(
                'productName'
            ).innerText =
                product.name;

            document.getElementById(
                'productDescription'
            ).innerText =
                product.description;

            document.getElementById(
                'productPrice'
            ).innerText =
                '₹' + product.price;

            document.getElementById(
                'productNotes'
            ).innerText =
                product.description;

            if (product.image) {

                document.getElementById(
                    'productImage'
                ).src =
                    'uploads/products/' +
                    product.image;

                document.getElementById(
                    'productImage'
                ).alt =
                    product.name;
            }
        })

        .catch(error => {

            console.error(error);

        });
} 


let qty = 1;

const qtyValue = document.getElementById('qtyValue');
const qtyPlus = document.getElementById('qtyPlus');
const qtyMinus = document.getElementById('qtyMinus');

qtyPlus.addEventListener('click', () => {
    qty++;
    qtyValue.innerText = qty;
});

qtyMinus.addEventListener('click', () => {
    if (qty > 1) {
        qty--;
        qtyValue.innerText = qty;
    }
});

