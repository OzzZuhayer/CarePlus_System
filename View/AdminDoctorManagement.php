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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Management — CarePlus Admin</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
<div class="layout">

    <?php include "AdminSidebar.php"; ?>

    <div class="main-content">

        <div class="page-header">
            <h1>Doctor Management</h1>
            <p>Add, edit or remove doctors and manage their availability.</p>
        </div>

        <!-- Messages -->
        <?php if ($errorMsg): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>
        <?php if ($successMsg): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon blue">👨‍⚕️</div>
                <div>
                    <div class="stat-value"><?= $stats['total_doctors'] ?? 0 ?></div>
                    <div class="stat-label">Total Enrolled Doctors</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">✅</div>
                <div>
                    <div class="stat-value"><?= $stats['active_doctors'] ?? 0 ?></div>
                    <div class="stat-label">Active Doctors</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">📅</div>
                <div>
                    <div class="stat-value"><?= $totalAppts ?></div>
                    <div class="stat-label">Total Appointments</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">🕐</div>
                <div>
                    <div class="stat-value"><?= $todayAppts ?></div>
                    <div class="stat-label">Today's Appointments</div>
                </div>
            </div>
        </div>
