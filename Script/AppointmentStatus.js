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
