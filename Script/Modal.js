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
}