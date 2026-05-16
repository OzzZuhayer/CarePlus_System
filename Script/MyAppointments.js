// Trigger cancel modal for a pending appointment
function cancelAppointment(appointmentId) {
    showConfirmModal(function() {
        doCancel(appointmentId);
    }, 'Are you sure you want to cancel this appointment?', 'cancel');
}

// Send cancel request via AJAX
function doCancel(appointmentId) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../Controller/AppointmentController.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    var badge = document.getElementById('status-badge-' + appointmentId);
                    if (badge) {
                        badge.textContent = 'Cancelled';
                        badge.className   = 'badge badge-cancelled';
                    }

                    var row = document.getElementById('appt-row-' + appointmentId);
                    if (row) {
                        row.setAttribute('data-status', 'cancelled');

                        // Replace cancel button with dash
                        var btn = row.querySelector('.btn-danger');
                        if (btn) btn.outerHTML = '<span style="color:#9ca3af; font-size:13px;">—</span>';

                        // Show cancellation note column
                        var noteCell = row.querySelector('.cancel-note-cell');
                        if (noteCell) noteCell.textContent = 'Cancelled by patient';
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            } catch (e) {
                alert('Something went wrong. Please try again.');
            }
        }
    };

    xhr.send('action=cancel_appointment&appointment_id=' + appointmentId);
}
