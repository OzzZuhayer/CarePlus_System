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
       <!-- Registration Form -->
        <form action="../Controller/RegisterController.php" method="POST" id="registerForm">

            <div class="form-group" style="margin-bottom: 14px;">
                <label for="user_name">Full Name</label>
                <input type="text" id="user_name" name="user_name" class="form-control"
                       placeholder="Enter your full name" required>
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label for="user_email">Email Address</label>
                <input type="email" id="user_email" name="user_email" class="form-control"
                       placeholder="Enter your email" required>
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label for="user_password">Password</label>
                <div class="password-wrap">
                    <input type="password" id="user_password" name="user_password" class="form-control"
                           placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('user_password', this)">Show</button>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label for="user_dob">Date of Birth</label>
                <input type="date" id="user_dob" name="user_dob" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label for="user_bg">Blood Group</label>
                <select id="user_bg" name="user_bg" class="form-control" required>
                    <option value="">Select blood group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="user_phone">Phone Number</label>
                <input type="text" id="user_phone" name="user_phone" class="form-control"
                       placeholder="Enter your phone number" required>
            </div>
            <!-- Client-side error message box (shown by JS) -->
            <div id="formError" class="alert alert-error" style="display:none;"></div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding: 12px;">
                Register
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="Login.php">Login here</a>
        </div>
    </div>
</div>
<script src="../Script/Validation.js"></script>
</body>
</html>