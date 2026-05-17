// Show the selected photo filename when a file is chosen
var photoInput = document.getElementById('photoInput');
if (photoInput) {
    photoInput.addEventListener('change', function() {
        var fileName = this.files[0] ? this.files[0].name : '';
        document.getElementById('photoName').textContent = fileName ? '📎 ' + fileName : '';
    });
}

// Reset the doctor form back to empty (for adding a new doctor)
function resetDoctorForm() {
    var form = document.getElementById('doctorForm');
    if (form) {
        form.reset();
        document.getElementById('photoName').textContent = '';
    }
}

// Search/filter doctor rows in the table by typing in the search box
function searchTable(inputId, tableId) {
    var input = document.getElementById(inputId).value.toLowerCase();
    var table = document.getElementById(tableId);
    var rows  = table.querySelectorAll('tbody tr');

    rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    });
}
