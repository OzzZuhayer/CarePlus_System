// Toggle password visibility (show/hide the password field)
function togglePassword(fieldId, button) {
    var field = document.getElementById(fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        button.textContent = 'Hide'; // change icon when visible
    } else {
        field.type = 'password';
        button.textContent = 'Show';
    }
}
