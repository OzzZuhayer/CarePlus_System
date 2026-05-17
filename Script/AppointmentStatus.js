// Shared by Doctor Dashboard and Admin pages — updates appointment status via AJAX

// Generic status update (doctor dashboard, AdminAppointments)
function updateAppointmentStatus(appointmentId, role, fixedStatus) {
    var newStatus = fixedStatus || document.getElementById('status-select-' + appointmentId)?.value;
    if (!newStatus) return;

    if (!confirm('Change status to "' + newStatus + '"?')) return;

    sendStatusUpdate(appointmentId, newStatus, null, false);
}

// Dashboard update — uses modal, shows cancel note textarea when Cancelled is selected
function dashboardUpdate(appointmentId) {
    var select = document.getElementById('status-select-' + appointmentId);
    if (!select) return;
    var newStatus = select.value;

    var isCancelling = (newStatus === 'Cancelled');
    var message = isCancelling ? 'Enter cancellation reason below. (Required)' : 'Confirm status update to "' + newStatus + '"?';

    showConfirmModal(function() {
        var note = null;
        if (isCancelling) {
            var noteInput = document.getElementById('modalNoteInput');
            note = noteInput ? noteInput.value.trim() : '';
        }
        sendStatusUpdate(appointmentId, newStatus, note, isCancelling);
    }, message, isCancelling ? 'cancel' : 'update', isCancelling);
}

// Send AJAX status update; if cancelled, removes the row from dashboard table
function sendStatusUpdate(appointmentId, newStatus, note, removeRow) {
    var xhr = new XMLHttpRequest();
    var url = '../Controller/UpdateAppointmentStatusController.php';
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.ok) {
                    if (removeRow) {
                        // Remove cancelled row from dashboard table via AJAX
                        var row = document.getElementById('appt-row-' + appointmentId);
                        if (row) row.remove();

                        // Show empty message if no rows left
                        var tbody = document.getElementById('dashboardTableBody');
                        if (tbody && tbody.querySelectorAll('tr').length === 0) {
                            tbody.innerHTML = "<tr><td colspan='7' style='text-align:center; color:#6b7280; padding:30px;'>No upcoming appointments for today or tomorrow.</td></tr>";
                        }
                    } else {
                        // Update badge in place
                        var badge = document.getElementById('status-badge-' + appointmentId);
                        if (badge) {
                            badge.textContent = response.new_status;
                            badge.className   = 'badge ' + getStatusBadgeClass(response.new_status);
                        }
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            } catch(e) {
                alert('Something went wrong.');
            }
        }
    };

    var body = 'appointment_id=' + appointmentId + '&new_status=' + encodeURIComponent(newStatus);
    if (note !== null && note !== '') {
        body += '&action=cancel_with_reason&reason=' + encodeURIComponent(note);
    }
    xhr.send(body);
}
