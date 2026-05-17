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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — CarePlus</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
<div class="layout">

    <?php include "AdminSidebar.php"; ?>

    <div class="main-content">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px;">
            <div>
                <h1 style="font-size:26px; font-weight:700; color:#0d1b2e;">Admin Dashboard</h1>
                <p style="color:#6b7280;">Manage all appointments, doctors and users.</p>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <div>
                    <div style="font-weight:600; font-size:14px; color:#0d1b2e;"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                    <div style="font-size:11px; color:#6b7280;">Administrator</div>
                </div>
                <a href="../Controller/LogoutController.php" class="docbar-logout">Logout</a>
            </div>
        </div>

        <!-- Stat cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon blue">👨‍⚕️</div>
                <div>
                    <div class="stat-value"><?= $doctorStats['total_doctors'] ?? 0 ?></div>
                    <div class="stat-label">Total Doctors</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">✅</div>
                <div>
                    <div class="stat-value"><?= $doctorStats['active_doctors'] ?? 0 ?></div>
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
        
