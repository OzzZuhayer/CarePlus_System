<?php
session_start();
include_once "../Config/SessionGuard.php";

if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Admin') {
    header("Location: Login.php");
    exit();
}

include_once "../Config/Db.php";
include_once "../Model/DoctorModel.php";
include_once "../Model/AppointmentModel.php";

$db = new Db();
$conn = $db->connection();
$doctorModel = new DoctorModel();
$appointmentModel = new AppointmentModel();

// Get all data needed for this page
$doctors         = $doctorModel->getAllDoctors($conn);
$specializations = $doctorModel->getAllSpecializations($conn);
$stats           = $doctorModel->getDoctorStats($conn);
$totalAppts      = $appointmentModel->getTotalAppointmentCount($conn);
$todayAppts      = $appointmentModel->getTodaysAppointmentCount($conn);

// Check if we are editing an existing doctor
$editingDoctor = null;
$selectedDays  = [];
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $editingDoctor = $doctorModel->getDoctorById($conn, (int)$_GET['edit_id']);
    if ($editingDoctor) {
        // Convert the comma-separated days string back into an array for the checkboxes
        $selectedDays = array_map('trim', explode(',', $editingDoctor['doctor_availability']));
    }
}

$errorMsg   = isset($_GET['error'])   ? urldecode($_GET['error'])   : '';
$successMsg = isset($_GET['success']) ? urldecode($_GET['success']) : '';

$activePage = 'doctors';
$allDays    = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>