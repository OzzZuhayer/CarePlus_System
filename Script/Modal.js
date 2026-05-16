// Holds the pending form or callback
var pendingForm = null;

// Show the confirm modal
// formOrCallback : a <form> element to submit, OR a callback function
// message        : body text shown in the modal
// type           : 'delete' | 'cancel' | 'deactivate' | 'update' — controls title and button label
// showNote       : if true, shows the cancellation note textarea
function showConfirmModal(formOrCallback, message, type, showNote) {
    if (typeof formOrCallback === 'function') {
        pendingForm = null;
        window.pendingCallback = formOrCallback;
    } else {
        pendingForm = formOrCallback;
        window.pendingCallback = null;
    }

    document.getElementById('modalMessage').textContent = message || 'Are you sure?';

    var titleEl  = document.getElementById('modalTitle');
    var btnEl    = document.getElementById('modalConfirmBtn');
    var noteWrap = document.getElementById('modalNoteWrap');
    var noteInput = document.getElementById('modalNoteInput');

    // Reset note textarea
    if (noteInput) noteInput.value = '';
    if (noteWrap) noteWrap.style.display = showNote ? 'block' : 'none';

    // Set title and confirm button label by type
    if (type === 'cancel') {
        titleEl.textContent = 'Confirm Cancellation';
        btnEl.textContent   = 'Yes';
    } else if (type === 'deactivate') {
        titleEl.textContent = 'Confirm Deactivate';
        btnEl.textContent   = 'Yes';
    } else if (type === 'update') {
        titleEl.textContent = 'Confirm Update';
        btnEl.textContent   = 'Yes';
    } else {
        titleEl.textContent = 'Confirm Delete';
        btnEl.textContent   = 'Yes';
    }

    document.getElementById('confirmModal').style.display  = 'flex';
    document.getElementById('modalBackdrop').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// Called when user clicks the confirm button
function confirmAction() {
    closeModal();
    if (pendingForm) {
        pendingForm.submit();
        pendingForm = null;
    } else if (window.pendingCallback) {
        window.pendingCallback();
        window.pendingCallback = null;
    }
}

// Close the modal
function closeModal() {
    document.getElementById('confirmModal').style.display  = 'none';
    document.getElementById('modalBackdrop').style.display = 'none';
    document.body.style.overflow = '';
    pendingForm = null;
}

// Close modal on backdrop click
document.addEventListener('DOMContentLoaded', function() {
    var backdrop = document.getElementById('modalBackdrop');
    if (backdrop) {
        backdrop.addEventListener('click', closeModal);
    }
});
