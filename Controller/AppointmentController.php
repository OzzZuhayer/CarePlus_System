<?php

session_start();
include_once "../Config/SessionGuard.php";

include_once "../Config/Db.php";
include_once "../Model/AppointmentModel.php";
include_once "../Model/DoctorModel.php";

// Only logged-in patients can book appointments
if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Patient') {
    header("Location: ../View/Login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $db = new Db();
    $conn = $db->connection();
    $appointmentModel = new AppointmentModel();

    $action = $_POST['action'] ?? '';

    // ACTION: Book an Appointment
    if ($action == 'book_appointment') {

        $patientId          = $_SESSION['user_id'];
        $doctorId           = (int)$_POST['doctor_id'];
        $appointmentDate    = trim($_POST['appointment_date']);
        $appointmentTime    = trim($_POST['appointment_time']);
        $appointmentMessage = trim($_POST['appointment_message']);

        // Validate all fields
        if ($doctorId <= 0 || empty($appointmentDate) || empty($appointmentTime)) {
            header("Location: ../View/BookAppointment.php?doctor_id=$doctorId&error=" . urlencode("Please fill all required fields."));
            exit();
        }

        if (empty($appointmentMessage)) {
            header("Location: ../View/BookAppointment.php?doctor_id=$doctorId&error=" . urlencode("Please describe your reason for visit."));
            exit();
        }

        // Make sure the appointment date is not in the past
        if ($appointmentDate < date('Y-m-d')) {
            header("Location: ../View/BookAppointment.php?doctor_id=$doctorId&error=" . urlencode("Cannot book an appointment in the past."));
            exit();
        }

        // Double-check that the slot is still available (prevents race conditions)
        if (!$appointmentModel->isSlotAvailable($conn, $doctorId, $appointmentDate, $appointmentTime)) {
            header("Location: ../View/BookAppointment.php?doctor_id=$doctorId&error=" . urlencode("This time slot was just taken. Please choose another slot."));
            exit();
        }

        // Save the appointment
        $newAppointmentId = $appointmentModel->bookAppointment($conn, $patientId, $doctorId, $appointmentDate, $appointmentTime, $appointmentMessage);

        if ($newAppointmentId) {
            header("Location: ../View/BookingConfirmation.php?appointment_id=" . $newAppointmentId);
        } else {
            header("Location: ../View/BookAppointment.php?doctor_id=$doctorId&error=" . urlencode("Booking failed. Please try again."));
        }
        exit();
    }

    // ACTION: Cancel an Appointment
    if ($action == 'cancel_appointment') {

        // This is called via AJAX — return JSON
        header('Content-Type: application/json');

        $patientId     = $_SESSION['user_id'];
        $appointmentId = (int)$_POST['appointment_id'];

        if ($appointmentId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid appointment.']);
            exit();
        }

        // Cancel the appointment (only works if it belongs to this patient and is Pending)
        $success = $appointmentModel->cancelAppointment($conn, $appointmentId, $patientId);

        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'Appointment cancelled successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not cancel this appointment.']);
        }
        exit();
    }

} else {
    header("Location: ../View/BrowseDoctors.php");
    exit();
}