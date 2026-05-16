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
// Show an inline error message inside a form
function showFormError(elementId, message) {
    var el = document.getElementById(elementId);
    if (el) {
        el.textContent = message;
        el.style.display = 'flex';
    }
}
// Hide a form error message
function hideFormError(elementId) {
    var el = document.getElementById(elementId);
    if (el) {
        el.style.display = 'none';
    }
}
