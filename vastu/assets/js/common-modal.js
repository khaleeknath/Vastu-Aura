let confirmCallback = null;
let confirmModal = null;

document.addEventListener("DOMContentLoaded", function () {

    confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
    );

});

function showConfirmModal(title, message, callback){

    document.getElementById("confirmModalTitle").innerText = title;

    document.getElementById("confirmModalMessage").innerText = message;

    confirmCallback = callback;


    confirmModal.show();
}

document.getElementById("confirmActionBtn")
.addEventListener("click", function(){
    

    if(confirmCallback){

        confirmCallback();

    }
    confirmModal.hide();
});

document.querySelectorAll(".logout-btn").forEach(button => {

    button.addEventListener("click", function(e) {

        e.preventDefault();

        const logoutUrl = this.dataset.logoutUrl;

        showConfirmModal(
            "Logout",
            "Are you sure you want to logout?",
            function () {
                window.location.href = logoutUrl;
            }
        );

    });

});