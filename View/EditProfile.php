<?php
session_start();
include_once "../Config/SessionGuard.php";

if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Patient') {
    header("Location: Login.php");
    exit();
}

include_once "../Config/Db.php";
include_once "../Model/UserModel.php";

$db = new Db();
$conn = $db->connection();
$userModel = new UserModel();

$userId = $_SESSION['user_id'];
$user   = $userModel->getUserById($conn, $userId);

$errorMsg   = isset($_GET['error'])   ? urldecode($_GET['error'])   : '';
$successMsg = isset($_GET['success']) ? urldecode($_GET['success']) : '';

$activePage = 'profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile — CarePlus Hospital</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="icon" type="image/x-icon" href="../Asset/Public/favicon.ico">
</head>
<body>
<div class="layout">

    <?php include "PatientSidebar.php"; ?>

    <div class="main-content">

        <div class="page-header">
            <h1>Edit Your Profile</h1>
            <p>Update your personal information.</p>
        </div>

        <!-- Messages -->
        <?php if ($errorMsg): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>
        <?php if ($successMsg): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>

        <!-- Profile Update Form -->
        <div class="card">
            <div class="card-title">Personal Information</div>
            <form action="../Controller/ProfileController.php" method="POST">
                <input type="hidden" name="action" value="update_profile">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="user_name">Full Name</label>
                        <input type="text" id="user_name" name="user_name" class="form-control"
                               value="<?= htmlspecialchars($user['user_name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="user_dob">Date of Birth</label>
                        <input type="date" id="user_dob" name="user_dob" class="form-control"
                               value="<?= htmlspecialchars($user['user_dob']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="user_email">Email</label>
                        <!-- Email is not editable — shown as read-only -->
                        <input type="email" id="user_email" class="form-control"
                               value="<?= htmlspecialchars($user['user_email']) ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="user_bg">Blood Group</label>
                        <select id="user_bg" name="user_bg" class="form-control" required>
                            <?php
                            $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                            foreach ($bloodGroups as $bg) {
                                $selected = ($user['user_bg'] == $bg) ? 'selected' : '';
                                echo "<option value=\"$bg\" $selected>$bg</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="user_phone">Phone Number</label>
                        <input type="text" id="user_phone" name="user_phone" class="form-control"
                               value="<?= htmlspecialchars($user['user_phone']) ?>" required>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="PatientHome.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>

        <!-- Password Change Form -->
        <div class="card">
            <div class="card-title">Change Password</div>
            <form action="../Controller/ProfileController.php" method="POST" id="passwordForm">
                <input type="hidden" name="action" value="change_password">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <div class="password-wrap">
                            <input type="password" id="current_password" name="current_password"
                                   class="form-control" placeholder="Enter current password">
                            <button type="button" class="password-toggle"
                                    onclick="togglePassword('current_password', this)">👁</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <div class="password-wrap">
                            <input type="password" id="new_password" name="new_password"
                                   class="form-control" placeholder="Enter new password">
                            <button type="button" class="password-toggle"
                                    onclick="togglePassword('new_password', this)">👁</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <div class="password-wrap">
                            <input type="password" id="confirm_password" name="confirm_password"
                                   class="form-control" placeholder="Confirm new password">
                            <button type="button" class="password-toggle"
                                    onclick="togglePassword('confirm_password', this)">👁</button>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>

    </div>
</div>

<script src="../Script/Validation.js"></script>
</body>
</html>
