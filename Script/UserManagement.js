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
// The actual AJAX toggle logic (runs after modal confirmation)
function doToggleUserStatus(userId) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../Controller/ToggleUserStatusController.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);

                if (response.status === 'success') {
                    var badge  = document.getElementById('status-badge-' + userId);
                    var button = document.getElementById('toggle-btn-' + userId);

                    if (response.new_status === 1) {
                        badge.textContent  = 'Active';
                        badge.className    = 'badge badge-active';
                        button.textContent = 'Deactivate';
                        button.className   = 'btn btn-sm btn-danger';
                    } else {
                        badge.textContent  = 'Inactive';
                        badge.className    = 'badge badge-inactive';
                        button.textContent = 'Activate';
                        button.className   = 'btn btn-sm btn-success';
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            } catch (e) {
                alert('Something went wrong. Please try again.');
            }
        }
    };

    xhr.send('user_id=' + userId);
}
