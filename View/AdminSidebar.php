<?php
// This file renders the sidebar for admin pages.
// Set $activePage before including to highlight the correct menu item.
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
        <a href="AdminDashboard.php" class="nav-item <?= ($activePage == 'dashboard') ? 'active' : '' ?>">
            <span class="nav-icon">📊</span> Dashboard
        </a>
        <a href="AdminDoctorManagement.php" class="nav-item <?= ($activePage == 'doctors') ? 'active' : '' ?>">
            <span class="nav-icon">👨‍⚕️</span> Doctors
        </a>
        <a href="AdminSpecializations.php" class="nav-item <?= ($activePage == 'specializations') ? 'active' : '' ?>">
            <span class="nav-icon">🏥</span> Specializations
        </a>
        <a href="AdminAppointments.php" class="nav-item <?= ($activePage == 'appointments') ? 'active' : '' ?>">
            <span class="nav-icon">📅</span> Appointments
        </a>
        <a href="AdminUsers.php" class="nav-item <?= ($activePage == 'users') ? 'active' : '' ?>">
            <span class="nav-icon">👥</span> Users
        </a>
        <a href="../Controller/LogoutController.php" class="nav-item logout-item">
            <span class="nav-icon">🚪</span> Logout
        </a>
    </nav>
</div>
