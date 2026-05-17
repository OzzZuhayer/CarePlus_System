<?php
session_start();
include_once "../Config/SessionGuard.php";

if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Admin') {
    header("Location: Login.php");
    exit();
}

include_once "../Config/Db.php";
include_once "../Model/AppointmentModel.php";
include_once "../Model/DoctorModel.php";
include_once "../Model/UserModel.php";

$db = new Db();
$conn = $db->connection();
$appointmentModel = new AppointmentModel();
$doctorModel      = new DoctorModel();
$userModel        = new UserModel();

// Dashboard table: today + tomorrow, Pending/Confirmed only
$dashboardAppointments = $appointmentModel->getDashboardAppointments($conn);
$doctorStats           = $doctorModel->getDoctorStats($conn);
$totalAppts            = $appointmentModel->getTotalAppointmentCount($conn);
$todayAppts            = $appointmentModel->getTodaysAppointmentCount($conn);

$activePage = 'dashboard';
?>