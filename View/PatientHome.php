
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — CarePlus Hospital</title>
    <link rel="stylesheet" href="Style.css">
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
