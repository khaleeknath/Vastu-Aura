document.getElementById('saveAll').addEventListener('click', function () {

    let rows = document.querySelectorAll('tbody tr');
    let data = [];

    rows.forEach(row => {

        let id = row.querySelector('.status-dropdown').dataset.id;
        let status = row.querySelector('.status-dropdown').value;
        let comment = row.querySelector('.comment-box').value;

        data.push({ id, status, comment });
    });

    fetch('assets/api/admin-appointment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ data })
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            alert(res.message);
            location.reload();
        } else {
            alert(res.message || "Error updating data");
        }
    });

});

document.getElementById("sidebarLogout").addEventListener("click", function(e) {
    e.preventDefault();

    let confirmLogout = confirm("Are you sure you want to logout?");

    if (confirmLogout) {
        window.location.href = "assets/api/logout.php";
    }
});