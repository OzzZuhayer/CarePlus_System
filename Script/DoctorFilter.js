// BrowseDoctors page — AJAX specialization filter and name search

function filterBySpecialization(specializationId) {
    var container = document.getElementById('doctorCardContainer');
    container.innerHTML = '<p style="color:#6b7280; padding:20px;">Loading...</p>';

    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../Controller/GetDoctorsController.php?specialization_id=' + specializationId, true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                container.innerHTML = '';

                if (response.ok && response.doctors.length > 0) {
                    response.doctors.forEach(function(doc) {
                        var photoSrc = doc.doctor_photo
                            ? '../' + doc.doctor_photo
                            : '../Asset/Public/Uploads/Doctors/default.png';

                        var card = document.createElement('div');
                        card.className = 'doctor-card';
                        card.setAttribute('data-name', doc.user_name.toLowerCase());
                        card.innerHTML =
                            '<img src="' + photoSrc + '" class="doctor-card-photo" alt="Dr. ' + doc.user_name + '">' +
                            '<div class="doctor-card-body">' +
                                '<div class="doctor-card-name">Dr. ' + doc.user_name + '</div>' +
                                '<div class="doctor-card-spec">' + doc.specialization_name + '</div>' +
                                '<div class="doctor-card-fee" style="margin-top:8px;">Fee: <strong>' + doc.doctor_fee + ' TK</strong></div>' +
                                '<div style="margin-top:14px;">' +
                                    '<a href="BookAppointment.php?doctor_id=' + doc.doctor_id + '" class="btn btn-primary" style="width:100%;justify-content:center;">View Profile</a>' +
                                '</div>' +
                            '</div>';
                        container.appendChild(card);
                    });
                } else {
                    container.innerHTML = '<div style="color:#6b7280; padding:20px;">No doctors found.</div>';
                }
            } catch(e) {
                container.innerHTML = '<div style="color:#b91c1c; padding:20px;">Failed to load doctors.</div>';
            }
        }
    };

    xhr.send();
}

// Name search filters visible cards
function searchTable(inputId, containerId) {
    var input     = document.getElementById(inputId).value.toLowerCase();
    var container = document.getElementById(containerId);
    var cards     = container.querySelectorAll('.doctor-card');
    cards.forEach(function(card) {
        var name = card.getAttribute('data-name') || '';
        card.style.display = name.includes(input) ? '' : 'none';
    });
}
