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

// Search/filter rows in a table by text content
function searchTable(inputId, tableId) {
    var input  = document.getElementById(inputId).value.toLowerCase();
    var table  = document.getElementById(tableId);

    // Check if it's a table or a card grid
    var rows = table.querySelectorAll('tbody tr');
    if (rows.length === 0) {
        // It's a card grid — filter cards instead
        var cards = table.querySelectorAll('.doctor-card');
        cards.forEach(function(card) {
            var name = card.getAttribute('data-name') || '';
            card.style.display = name.includes(input) ? '' : 'none';
        });
        return;
    }

    rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    });
}

// Registration form client-side validation
var registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
        var name     = document.getElementById('user_name').value.trim();
        var email    = document.getElementById('user_email').value.trim();
        var password = document.getElementById('user_password').value.trim();
        var dob      = document.getElementById('user_dob').value;
        var bg       = document.getElementById('user_bg').value;
        var phone    = document.getElementById('user_phone').value.trim();
        var errors   = [];

        if (!name) 
            errors.push('Full name is required.');
        if (!email || !email.includes('@')) 
            errors.push('Valid email is required.');
        if (!password || password.length < 8) 
            errors.push('Password must be at least 8 characters.');
        if (!dob) 
            errors.push('Date of birth is required.');
        if (!bg) 
            errors.push('Blood group is required.');
        if (!phone) 
            errors.push('Phone number is required.');

        if (errors.length > 0) {
            e.preventDefault(); // Stop the form from submitting
            showFormError('formError', errors.join(' '));
        }
    });
}

var loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        var email    = document.getElementById('user_email').value.trim();
        var password = document.getElementById('user_password').value.trim();
        var errors   = [];

        if (!email || !email.includes('@')) 
            errors.push('Valid email is required.');
        if (!password || password.length < 8) 
            errors.push('Password must be at least 8 characters.');

        if (errors.length > 0) {
            e.preventDefault(); // Stop the form from submitting
            showFormError('formError', errors.join(' '));
        }
    });
}
