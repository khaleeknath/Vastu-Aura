
document.getElementById("appointmentLogout").addEventListener("click", function(e) {
    e.preventDefault();
  
    let confirmLogout = confirm("Are you sure you want to logout?");
  
    if (confirmLogout) {
        window.location.href = "assets/api/logout.php";
    }
  });

document.addEventListener("DOMContentLoaded", loadAppointments);

function loadAppointments(){

    fetch("assets/api/myappointments.php")

    .then(res=>res.json())

    .then(res=>{

        const container=document.getElementById("appointmentsContainer");

        if(!res.success){

            container.innerHTML=`
                <div class="alert alert-danger">
                    ${res.message}
                </div>
            `;

            return;

        }

        if(res.appointments.length===0){

            container.innerHTML=`

                <div class="text-center py-5">

                    <h2>📅</h2>

                    <h3>No Appointments Found</h3>

                    <p>

                        You haven't booked any consultation yet.

                    </p>

                    <a href="booking.php"
                       class="btn btn-brand">

                        Book Appointment

                    </a>

                </div>

            `;

            return;

        }

        let html="";

        res.appointments.forEach(app=>{

            let badge="";

            switch(app.status){

                case "pending":
                    badge="bg-warning text-dark";
                    break;

                case "approved":
                    badge="bg-primary";
                    break;

                case "completed":
                    badge="bg-success";
                    break;

                case "cancelled":
                    badge="bg-danger";
                    break;

                case "rejected":
                    badge="bg-secondary";
                    break;

                default:
                    badge="bg-dark";
            }

            html+=`

            <div class="order-card">

                <div class="row align-items-center">

                    <div class="col-lg-8">

                        <div class="d-flex justify-content-between">

                            <h4>

                                📅 Appointment #VA-${String(app.id).padStart(6,"0")}

                            </h4>

                            <span class="badge ${badge}">

                                ${app.status}

                            </span>

                        </div>

                        <hr>

                        <div class="row">

                            <div class="col-md-4">

                                <small>Consultation</small>

                                <h6>

                                    ${app.consultation_type}

                                </h6>

                            </div>

                            <div class="col-md-4">

                                <small> Prefered Date</small>

                                <h6>

                                    ${app.preferred_date}

                                </h6>

                            </div>

                            <div class="col-md-4">

                                <small> Prefered Time</small>

                                <h6>

                                    ${app.preferred_time}

                                </h6>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

                        <a href="appointment-status.php?id=${app.id}"

                           class="btn btn-brand">

                            View Details

                        </a>

                    </div>

                </div>

            </div>

            `;

        });

        container.innerHTML=html;

    })

    .catch(err=>{

        console.log(err);

    });

}