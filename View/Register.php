<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — CarePlus Hospital</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">

        <!-- Logo -->
        <div class="auth-logo">
            <svg width="36" height="36" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="8" fill="#1a56db"/>
                <path d="M16 7v18M7 16h18" stroke="white" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <div>
                <div class="auth-logo-text">CarePlus Hospital</div>
                <div class="auth-logo-sub">Appointment Booking System</div>
            </div>
        </div>

        <h2 class="auth-title">Create Your Account</h2>
        <p class="auth-subtitle">Register as a new patient</p>

        <!-- Show error message if any -->
        <?php if ($errorMsg): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <!-- Show success message if any -->
        <?php if ($successMsg): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>
