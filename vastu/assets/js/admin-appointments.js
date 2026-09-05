document.addEventListener('DOMContentLoaded', function () {
    const appointmentModalEl = document.getElementById('appointmentModal');
    const appointmentModal = new bootstrap.Modal(appointmentModalEl);

    // Open modal with that row's data
    document.querySelectorAll('.view-appointment').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('modalAppointmentId').value = this.dataset.id;
            document.getElementById('modalName').textContent = this.dataset.name;
            document.getElementById('modalMobile').textContent = this.dataset.mobile;
            document.getElementById('modalDate').textContent = this.dataset.date;
            document.getElementById('modalTime').textContent = this.dataset.time;
            document.getElementById('modalStatus').value = this.dataset.status;
            document.getElementById('modalComment').value = this.dataset.comment;
            appointmentModal.show();
        });
    });

    // Update just this one appointment
    document.getElementById('modalUpdateBtn').addEventListener('click', function () {
        const btn = this;
        const payload = {
            id: document.getElementById('modalAppointmentId').value,
            status: document.getElementById('modalStatus').value,
            comment: document.getElementById('modalComment').value
        };

        btn.disabled = true;
        btn.textContent = 'Updating...';

        fetch('assets/api/admin-appointment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(res => {
            btn.disabled = false;
            btn.textContent = 'Update & Notify';
            if (res.success) {
                alert(res.message || 'Appointment updated and client notified.');
                appointmentModal.hide();
                location.reload();
            } else {
                alert(res.message || 'Error updating appointment');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.textContent = 'Update & Notify';
            alert('Network error. Please try again.');
        });
    });

    // Logout (unchanged, just guarded with ?.)
    document.getElementById('sidebarLogout')?.addEventListener('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'assets/api/logout.php';
        }
    });
});