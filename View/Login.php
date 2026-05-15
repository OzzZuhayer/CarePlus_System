 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CarePlus Hospital</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">

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

        <h2 class="auth-title">Welcome Back!</h2>
        <p class="auth-subtitle">Login to your account</p>

        <?php if ($errorMsg): ?>
            <div class="alert alert-error">🚫 <?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>
        <?php if ($successMsg): ?>
            <div class="alert alert-success">✔️ <?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>

        <form action="../Controller/LoginController.php" method="POST" id= "loginForm">

            <div class="form-group" style="margin-bottom:14px;">
                <label for="user_email">Email Address</label>
                <input type="email" id="user_email" name="user_email" class="form-control"
                       placeholder="Enter your email" required>
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label for="user_password">Password</label>
                <div class="password-wrap">
                    <input type="password" id="user_password" name="user_password" class="form-control"
                           placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('user_password', this)">Show</button>
                </div>
            </div>
            <div id="formError" class="alert alert-error" style="display:none;"></div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:12px;">
                Login
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="Register.php">Register here</a>
        </div>

        <!-- DEMO ACCOUNTS -->
        <div class="demo-box">
            <h4>Demo Accounts</h4>
            <div class="demo-account patient">
                👤 Patient: patient@example.com / 12345678
            </div>
            <div class="demo-account doctor">
                👨‍⚕️ Doctor: doctor@example.com / 12345678
            </div>
            <div class="demo-account admin">
                🛡️ Admin: admin@example.com / 12345678
            </div>
        </div>
        <!-- END DEMO ACCOUNTS -->

    </div>
</div>
<script src="../Script/Validation.js"></script>
</body>
</html>
