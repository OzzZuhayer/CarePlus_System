<?php
    // Side bar for Patient views
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="8" fill="#1a56db"/>
                <path d="M16 7v18M7 16h18" stroke="white" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>
        <div>
            <div class="brand-name">CarePlus</div>
            <div class="brand-sub">Hospital System</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="PatientHome.php" class="nav-item <?= ($activePage == 'dashboard') ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span> Dashboard
        </a>
        <a href="MyAppointments.php" class="nav-item <?= ($activePage == 'appointments') ? 'active' : '' ?>">
            <span class="nav-icon">📅</span> My Appointments
        </a>
        <a href="BrowseDoctors.php" class="nav-item <?= ($activePage == 'doctors') ? 'active' : '' ?>">
            <span class="nav-icon">🩺</span> Find Doctors
        </a>
        <a href="EditProfile.php" class="nav-item <?= ($activePage == 'profile') ? 'active' : '' ?>">
            <span class="nav-icon">👤</span> Profile
        </a>
        <a href="../Controller/LogoutController.php" class="nav-item logout-item">
            <span class="nav-icon">🚪</span> Logout
        </a>
    </nav>
</div>
