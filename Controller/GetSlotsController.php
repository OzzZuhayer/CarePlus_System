<?php

session_start();

include_once "../Config/Db.php";
include_once "../Model/AppointmentModel.php";
include_once "../Model/DoctorModel.php";

// This file is called via AJAX and always returns JSON
header('Content-Type: application/json');

// Only logged-in users can fetch slots
if (!isset($_SESSION['loggedIn'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit();
}

$doctorId        = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;
$appointmentDate = isset($_GET['date']) ? trim($_GET['date']) : '';

if ($doctorId <= 0 || empty($appointmentDate)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing doctor or date.']);
    exit();
}

$db = new Db();
$conn = $db->connection();
$doctorModel = new DoctorModel();
$appointmentModel = new AppointmentModel();

// Get the doctor's availability days (e.g. "Monday,Wednesday,Friday")
$doctor = $doctorModel->getDoctorById($conn, $doctorId);

if (!$doctor) {
    echo json_encode(['status' => 'error', 'message' => 'Doctor not found.']);
    exit();
}

// Check if the selected date falls on one of the doctor's available days
$availableDays = array_map('trim', explode(',', $doctor['doctor_availability']));
$dayOfWeek = date('l', strtotime($appointmentDate)); // example: "Monday"

if (!in_array($dayOfWeek, $availableDays)) {
    // Doctor is not available on this day
    echo json_encode(['status' => 'success', 'slots' => [], 'message' => 'Doctor not available on this day.']);
    exit();
}

// Define all possible time slots (every 30 minutes from 9am to 6pm)
$allSlots = [
    '09:00:00', '09:30:00', '10:00:00', '10:30:00', '11:00:00', '11:30:00',
    '12:00:00', '12:30:00', '13:00:00', '13:30:00', '14:00:00', '14:30:00',
    '15:00:00', '15:30:00', '16:00:00', '16:30:00', '17:00:00', '17:30:00'
];

// Get the slots that are already booked for this doctor on this date
$bookedSlots = $appointmentModel->getBookedSlots($conn, $doctorId, $appointmentDate);

// Build a list of available slots (remove booked ones)
$availableSlots = [];
foreach ($allSlots as $slot) {
    if (!in_array($slot, $bookedSlots)) {
        // Format the time nicely for display (e.g. "09:00 AM")
        $availableSlots[] = [
            'value'   => $slot,
            'display' => date('h:i A', strtotime($slot))
        ];
    }
}

echo json_encode(['status' => 'success', 'slots' => $availableSlots]);
exit();
