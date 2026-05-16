// Show the modal first, then run the AJAX if confirmed
function toggleUserStatus(userId) {
    var badge    = document.getElementById('status-badge-' + userId);
    var isActive = badge.textContent.trim() === 'Active';
    var msg      = isActive ? 'Deactivate this user account?' : 'Activate this user account?';

    // Pass the actual AJAX call as a callback — modal will run it on confirm
    showConfirmModal(function() {
        doToggleUserStatus(userId);
    }, msg, 'deactivate');
}
