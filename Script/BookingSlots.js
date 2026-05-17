var selectedDateValue = '';
var selectedTimeValue = '';

// Called when the user clicks a date button
function selectDate(button, date, doctorId) {
    // Highlight the selected date button
    var allDateBtns = document.querySelectorAll('.date-btn');
    allDateBtns.forEach(function(btn) {
        btn.classList.remove('selected');
    });
    button.classList.add('selected');

    selectedDateValue = date;

    // Hide the booking form until a time is selected
    document.getElementById('bookingFormSection').style.display = 'none';
    document.getElementById('timeSlotSection').style.display    = 'block';
    document.getElementById('loadingSlots').style.display       = 'block';
    document.getElementById('timeSlotsContainer').innerHTML     = '';

    // Update the section title with the selected date
    document.getElementById('timeSlotsTitle').textContent =
        'Available Time Slots for ' + button.querySelector('.date-day').textContent +
        ' ' + button.querySelector('.date-num').textContent +
        ' ' + button.querySelector('.date-month').textContent;

    // Fetch available slots from the server via AJAX
    fetchAvailableSlots(doctorId, date);
}

// AJAX request to get available slots for this doctor on this date
function fetchAvailableSlots(doctorId, date) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../Controller/GetSlotsController.php?doctor_id=' + doctorId + '&date=' + date, true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                document.getElementById('loadingSlots').style.display = 'none';

                if (response.status === 'success') {
                    renderTimeSlots(response.slots);
                } else {
                    document.getElementById('timeSlotsContainer').innerHTML =
                        '<p style="color:#b91c1c;">' + (response.message || 'No slots available.') + '</p>';
                }
            } catch (e) {
                document.getElementById('timeSlotsContainer').innerHTML =
                    '<p style="color:#b91c1c;">Could not load slots. Please try again.</p>';
            }
        }
    };

    xhr.send();
}

// Render time slot buttons from the server response
function renderTimeSlots(slots) {
    var container = document.getElementById('timeSlotsContainer');
    container.innerHTML = '';

    if (slots.length === 0) {
        container.innerHTML = '<p style="color:#6b7280;">No available slots for this date.</p>';
        return;
    }

    // Creates button for each available slot
    slots.forEach(function(slot) {
        var btn = document.createElement('button');
        btn.type        = 'button';
        btn.className   = 'time-btn';
        btn.textContent = slot.display; // e.g. "09:00 AM"
        btn.setAttribute('data-time', slot.value);

        btn.onclick = function() {
            selectTimeSlot(this, slot.value, slot.display);
        };

        container.appendChild(btn);
    });
}

// Called when the user clicks a time slot button
function selectTimeSlot(button, timeValue, timeDisplay) {
    // Highlight the selected time button
    var allTimeBtns = document.querySelectorAll('.time-btn');
    allTimeBtns.forEach(function(btn) {
        btn.classList.remove('selected');
    });
    button.classList.add('selected');

    selectedTimeValue = timeValue;

    // Fill in the hidden form fields
    document.getElementById('selectedDate').value = selectedDateValue;
    document.getElementById('selectedTime').value = timeValue;

    // Show the booking form with a display of what was selected
    document.getElementById('selectedDateTimeDisplay').value =
        selectedDateValue + ' at ' + timeDisplay;
    document.getElementById('bookingFormSection').style.display = 'block';

    // Scroll down to the form smoothly
    document.getElementById('bookingFormSection').scrollIntoView({ behavior: 'smooth' });
}

// Character counter for the reason textarea
var messageInput = document.getElementById('appointmentMessage');
if (messageInput) {
    messageInput.addEventListener('input', function() {
        var count = this.value.length;
        document.getElementById('charCount').textContent = count + ' / 200';
    });
}
