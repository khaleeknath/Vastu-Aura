document.getElementById("invoiceLogout").addEventListener("click", function(e) {
  e.preventDefault();

  let confirmLogout = confirm("Are you sure you want to logout?");

  if (confirmLogout) {
      window.location.href = "assets/api/logout.php";
  }
});

document.addEventListener("DOMContentLoaded", function () {


  loadInvoice();


  document.getElementById("downloadInvoice")
  .addEventListener("click", function () {


      generatePDF(false);


  });



  document.getElementById("printInvoice")
  .addEventListener("click", function () {


      generatePDF(true);


  });



});



function generatePDF(print = false) {


  const invoice =
      document.getElementById("invoiceCard");


  if (!invoice) {

      alert("Invoice not found");

      return;
  }



  const options = {

      margin: 10,

      filename: "VastuAura-Invoice.pdf",


      image: {

          type: "jpeg",

          quality: 1

      },


      html2canvas: {

          scale: 2,

          useCORS: true,

          scrollY:0

      },


      jsPDF: {

          unit:"mm",

          format:"a4",

          orientation:"portrait"

      }

  };



  html2pdf()

  .set(options)

  .from(invoice)

  .toPdf()

  .get("pdf")

  .then(function(pdf){


      if(print){


          const blob =
          pdf.output("blob");


          const url =
          URL.createObjectURL(blob);


          window.open(url);


      }

      else{


          pdf.save(
              "VastuAura-Invoice.pdf"
          );

      }


  });


}




function loadInvoice(){


  const params =
  new URLSearchParams(
      window.location.search
  );


  const orderId =
  params.get("order_id");



  fetch(
    `assets/api/invoice.php?order_id=${orderId}`
  )


  .then(res=>res.json())


  .then(data=>{


      console.log(data);



      document.getElementById(
          "invoiceNumber"
      ).innerHTML =
      "Invoice #VA-"+data.order.id;



      document.getElementById(
          "invoiceCustomer"
      ).innerHTML =
      data.order.customer;



      document.getElementById(
          "invoiceEmail"
      ).innerHTML =
      data.order.email;



      document.getElementById(
          "invoiceDate"
      ).innerHTML =
      data.order.date;



      document.getElementById(
          "invoiceStatus"
      ).innerHTML =
      data.order.payment_status;



      let html="";


      data.items.forEach(item=>{


          html += `

          <div class="invoice-item">

              <span>
              ${item.name}
              </span>


              <span>
              ${item.qty} x ₹${item.price}
              </span>


              <strong>
              ₹${item.qty * item.price}
              </strong>


          </div>

          `;


      });



      document.getElementById(
          "invoiceItems"
      ).innerHTML = html;



      document.getElementById(
          "invoiceSubtotal"
      ).innerHTML =
      "₹"+data.order.total;



      document.getElementById(
          "invoiceTotal"
      ).innerHTML =
      "₹"+(Number(data.order.total)+150);



  });


}

document.addEventListener("DOMContentLoaded", loadInvoice);

function loadInvoice(){

    const params=new URLSearchParams(window.location.search);

    const orderId=params.get("order_id");

    fetch("assets/api/invoice.php?order_id=" + orderId)

    .then(res=>res.json())

    .then(res=>{

        if(!res.success)return;

        const order=res.order;

        document.getElementById("invoiceNumber").innerHTML=
        "Invoice #VA-"+String(order.id).padStart(6,"0");

        document.getElementById("invoiceDate").innerHTML=
        order.date;

        document.getElementById("invoiceCustomer").innerHTML=
        order.customer;

        document.getElementById("invoiceEmail").innerHTML=
        order.email;

        document.getElementById("invoiceStatus").innerHTML=
        order.payment_status;

        let html="";

        let subtotal=0;

        res.items.forEach(item=>{

            let total=item.price*item.qty;

            subtotal+=total;

            html+=`

            <div class="d-flex justify-content-between border-bottom py-3">

                <div>

                    <strong>${item.name}</strong>

                    <br>

                    ₹${item.price} × ${item.qty}

                </div>

                <strong>

                    ₹${total}

                </strong>

            </div>

            `;

        });

        document.getElementById("invoiceItems").innerHTML=html;

        document.getElementById("invoiceSubtotal").innerHTML=
        "₹"+subtotal;

        document.getElementById("invoiceTotal").innerHTML=
        "₹"+(subtotal+150);

    });

}