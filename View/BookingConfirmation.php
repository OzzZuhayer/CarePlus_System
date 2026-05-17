<?php
session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Patient') {
    header("Location: Login.php");
    exit();
}

// Get the appointment ID from the URL
$appointmentId = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;

if ($appointmentId <= 0) {
    header("Location: BrowseDoctors.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed — CarePlus</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="icon" type="image/x-icon" href="../Asset/Public/favicon.ico">
</head>
<body>
<div class="auth-page">
    <div class="auth-card" style="text-align:center; max-width:460px;">

        <!-- Success icon -->
        <div style="width:72px; height:72px; background:#ecfdf5; border-radius:50%;
                    display:flex; align-items:center; justify-content:center;
                    font-size:36px; margin:0 auto 20px;">
            ✅
        </div>

        <h2 style="color:#15803d; font-size:24px; font-weight:700; margin-bottom:8px;">
            Appointment Booked!
        </h2>
        <p style="color:#6b7280; margin-bottom:20px;">
            Your appointment has been successfully booked and is now pending confirmation.
        </p>

        <!-- Appointment ID badge -->
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px;
                    padding:14px; margin-bottom:24px;">
            <div style="font-size:13px; color:#6b7280;">Appointment ID</div>
            <div style="font-size:26px; font-weight:800; color:#15803d;">APPT-<?= str_pad($appointmentId, 4, '0', STR_PAD_LEFT) ?></div>
            <div style="font-size:13px; color:#6b7280; margin-top:4px;">
                Keep this ID for your records.
            </div>
        </div>

        <div style="display:flex; gap:10px; justify-content:center;">
            <a href="MyAppointments.php" class="btn btn-primary">View My Appointments</a>
            <a href="BrowseDoctors.php" class="btn btn-secondary">Find Another Doctor</a>
        </div>

    </div>
</div>
</body>
</html>
