<?php
session_start();
include_once "../Config/SessionGuard.php";

// Only patients can see this page
if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Patient') {
    header("Location: Login.php");
    exit();
}

include_once "../Config/Db.php";
include_once "../Model/UserModel.php";
include_once "../Model/AppointmentModel.php";

$db = new Db();
$conn = $db->connection();
$userModel = new UserModel();
$appointmentModel = new AppointmentModel();

$userId = $_SESSION['user_id'];

// Get the patient's full profile
$user = $userModel->getUserById($conn, $userId);

// Get appointment counts for the stat cards
$upcomingCount  = $appointmentModel->countUpcomingAppointments($conn, $userId);
$completedCount = $appointmentModel->countCompletedAppointments($conn, $userId);
$cancelledCount = $appointmentModel->countCancelledAppointments($conn, $userId);

// Format the "member since" date nicely
$memberSince = date('d M Y', strtotime($user['user_created_at']));
$formattedDob = date('d M Y', strtotime($user['user_dob']));

$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — CarePlus Hospital</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="icon" type="image/x-icon" href="../Asset/Public/favicon.ico">
</head>
<body>
<div class="layout">

    <!-- Sidebar -->
    <?php include "PatientSidebar.php"; ?>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Top bar with user info -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px;">
            <div>
                <h1 style="font-size:26px; font-weight:700; color:#0d1b2e;">
                    Welcome, <?= htmlspecialchars($user['user_name']) ?>! 👋
                </h1>
                <p style="color:#6b7280; margin-top:4px;">Here is your account overview.</p>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <div>
                    <div style="font-weight:600; font-size:14px; color:#0d1b2e;"><?= htmlspecialchars($user['user_name']) ?></div>
                    <div style="font-size:11px; color:#6b7280;">Patient</div>
                </div>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon blue">📅</div>
                <div>
                    <div class="stat-value"><?= $upcomingCount ?></div>
                    <div class="stat-label">Upcoming Appointments</div>
                    <a href="MyAppointments.php" class="stat-link">View Appointments →</a>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">✅</div>
                <div>
                    <div class="stat-value"><?= $completedCount ?></div>
                    <div class="stat-label">Completed Appointments</div>
                    <a href="MyAppointments.php?status=Completed" class="stat-link">View History →</a>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">❌</div>
                <div>
                    <div class="stat-value"><?= $cancelledCount ?></div>
                    <div class="stat-label">Cancelled Appointments</div>
                    <a href="MyAppointments.php?status=Cancelled" class="stat-link">View History →</a>
                </div>
            </div>
        </div>

        <!-- Profile Summary Card -->
        <div class="card">
            <div class="card-title">Your Profile Summary</div>
            <div class="profile-grid">
                <div class="profile-field">
                    <label>Full Name</label>
                    <span><?= htmlspecialchars($user['user_name']) ?></span>
                </div>
                <div class="profile-field">
                    <label>Date of Birth</label>
                    <span><?= $formattedDob ?></span>
                </div>
                <div class="profile-field">
                    <label>Email</label>
                    <span><?= htmlspecialchars($user['user_email']) ?></span>
                </div>
                <div class="profile-field">
                    <label>Blood Group</label>
                    <span><?= htmlspecialchars($user['user_bg']) ?></span>
                </div>
                <div class="profile-field">
                    <label>Phone</label>
                    <span><?= htmlspecialchars($user['user_phone']) ?></span>
                </div>
                <div class="profile-field">
                    <label>Member Since</label>
                    <span><?= $memberSince ?></span>
                </div>
            </div>

            <div style="margin-top:20px;">
                <a href="EditProfile.php" class="btn btn-primary">Edit Profile</a>
            </div>
        </div>

    </div><!-- end main-content -->
</div><!-- end layout -->
</body>
</html>
