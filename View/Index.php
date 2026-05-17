<?php
session_start();
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
    if ($_SESSION['user_role'] == 'Admin')       
        header("Location: AdminDashboard.php");
    elseif ($_SESSION['user_role'] == 'Doctor')  
        header("Location: DoctorDashboard.php");
    else                                          
        header("Location: PatientHome.php");
    exit();
}
include_once "../Config/Db.php";
include_once "../Model/DoctorModel.php";
include_once "../Model/AppointmentModel.php";
$db = new Db();
$conn = $db->connection();
$doctorModel      = new DoctorModel();
$appointmentModel = new AppointmentModel();
$specializations  = $doctorModel->getAllSpecializations($conn);
$topDoctors       = $doctorModel->getTopDoctors($conn, 3);
$completedCount   = $appointmentModel->getCompletedAppointmentCount($conn);
$doctorStats      = $doctorModel->getDoctorStats($conn);
$specCount        = $specializations->num_rows;
$specializations->data_seek(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarePlus — Hospital Appointment Booking</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="icon" type="image/x-icon" href="../Asset/Public/favicon.ico">
</head>
<body>

<nav class="home-nav">
    <div class="home-nav-brand">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
            <rect width="32" height="32" rx="8" fill="#1a56db"/>
            <path d="M16 7v18M7 16h18" stroke="white" stroke-width="3" stroke-linecap="round"/>
        </svg>
        CarePlus
    </div>
    <div class="home-nav-links">
        <a href="#specializations">Specializations</a>
        <a href="#doctors">Doctors</a>
        <a href="Login.php">Login</a>
        <a href="Register.php" class="btn-nav">Get Started</a>
    </div>
</nav>

<div class="hero">
    <div class="hero-content">
        <div class="hero-badge">🏥 Trusted Hospital Appointment System</div>
        <h1>Your Health, Our <span>Priority</span></h1>
        <p class="hero-sub">Book appointments with top specialists in minutes. CarePlus connects you with the right doctor at the right time.</p>
        <div class="hero-actions">
            <a href="Register.php" class="btn-hero-primary">Book an Appointment</a>
            <a href="Login.php" class="btn-hero-secondary">Login to Account</a>
        </div>
    </div>
</div>

<!-- Stat Bar -->
<div class="stat-bar">
    <div class="stat-bar-item">
        <div class="stat-bar-value"><?= number_format($completedCount) ?>+</div>
        <div class="stat-bar-label">Appointments Served</div>
    </div>
    <div class="stat-bar-item">
        <div class="stat-bar-value"><?= $doctorStats['active_doctors'] ?? 0 ?>+</div>
        <div class="stat-bar-label">Active Doctors</div>
    </div>
    <div class="stat-bar-item">
        <div class="stat-bar-value"><?= $specCount ?>+</div>
        <div class="stat-bar-label">Specializations</div>
    </div>
</div>

<!-- Specializations -->
<div id="specializations" style="background:#f3f6fb; padding:60px 0;">
    <div class="section" style="padding-top:0; padding-bottom:0;">
        <div class="section-title">Our Specializations</div>
        <p class="section-sub">We cover a wide range of medical specialties to serve all your healthcare needs.</p>
        <?php
        $specIcons = [
            'cardiology'=>'🫀',
            'orthopedics'=>'🦴',
            'neurology'=>'🧠',
            'pediatrics'=>'👶',
            'dermatology'=>'🦠',
            'general medicine'=>'💊',
            'gynecology'=>'🤰🏽',
            'ophthalmology'=>'👁️',
            'dentistry'=>'🦷',
            'psychiatry'=>'🧘',
            'urology'=>'⚕️',
            'oncology'=>'🎗️',
        ];
        ?>
        <div class="spec-grid">
            <?php 
            while ($spec = $specializations->fetch_assoc()):
                $key = strtolower($spec['specialization_name']);
                $icon = '🏥';
                foreach ($specIcons as $kw => $em) { 
                    if (str_contains($key, $kw)) { 
                        $icon = $em; 
                        break; 
                    } 
                }
            ?>
            <div class="spec-pill">
                <span class="spec-pill-icon"><?= $icon ?></span>
                <?= htmlspecialchars($spec['specialization_name']) ?>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- Featured Doctors -->
<div id="doctors" style="background:#fff; padding:60px 0; border-top:1px solid #e5e9f0;">
    <div class="section" style="padding-top:0; padding-bottom:0;">
        <div class="section-title">Featured Doctors</div>
        <p class="section-sub">Our most experienced doctors, ranked by appointments served.</p>
        <div class="featured-grid">
            <?php if ($topDoctors && $topDoctors->num_rows > 0): ?>
                <?php 
                while ($doc = $topDoctors->fetch_assoc()):
                    $photoSrc = !empty($doc['doctor_photo']) ? '../' . $doc['doctor_photo']: '../Asset/Public/Uploads/Doctors/default.png';
                    $bio = $doc['doctor_bio'] ? htmlspecialchars(substr($doc['doctor_bio'], 0, 80)) . '...' : 'Experienced specialist dedicated to patient care.';
                ?>
                <div class="featured-card">
                    <img src="<?= htmlspecialchars($photoSrc) ?>" class="featured-photo" alt="<?= htmlspecialchars($doc['user_name']) ?>">
                    <div class="featured-body">
                        <div class="featured-name"><?= htmlspecialchars($doc['user_name']) ?></div>
                        <div class="featured-spec"><?= htmlspecialchars($doc['specialization_name']) ?></div>
                        <p style="font-size:13px;color:#6b7280;margin-top:10px;line-height:1.5;"><?= $bio ?></p>
                        <div class="featured-meta">
                            <span>Fee: <strong><?= htmlspecialchars($doc['doctor_fee']) ?> TK</strong></span>
                            <span class="featured-appt">✅ <?= $doc['total_appointments'] ?> served</span>
                        </div>
                        <a href="Register.php" class="btn btn-primary"
                           style="width:100%;justify-content:center;margin-top:14px;display:flex;">
                            Book Appointment
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color:#6b7280;">No doctors available yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="home-footer">
    © <?= date('Y') ?> <span>CarePlus Hospital</span> — All rights reserved.
</div>
</body>
</html>
