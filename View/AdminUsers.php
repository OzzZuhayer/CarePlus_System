<?php
session_start();
include_once "../Config/SessionGuard.php";

if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Admin') {
    header("Location: Login.php");
    exit();
}

include_once "../Config/Db.php";
include_once "../Model/UserModel.php";

$db = new Db();
$conn = $db->connection();
$userModel = new UserModel();

// Get all users for the table
$users = $userModel->getAllUsers($conn);

$activePage = 'users';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management — CarePlus Admin</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
<div class="layout">

    <?php include "AdminSidebar.php"; ?>

    <div class="main-content">

        <div class="page-header">
            <h1>User Management</h1>
            <p>Manage all system users and their account status.</p>
        </div>

        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <div class="card-title" style="margin-bottom:0; border:none; padding:0;">All Users</div>
                <input type="text" id="userSearch" class="form-control"
                       placeholder="Search by name, email or role..."
                       style="max-width:260px;"
                       onkeyup="searchTable('userSearch', 'userTable')">
            </div>
            <div class="table-wrap">
                <table id="userTable">
                    <thead>
                        <tr>
                            <th>USER ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users && $users->num_rows > 0): ?>
                            <?php while ($row = $users->fetch_assoc()): ?>
                            <tr id="user-row-<?= $row['user_id'] ?>">
                                <td><?= 'U-' . str_pad($row['user_id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td><?= htmlspecialchars($row['user_name']) ?></td>
                                <td><?= htmlspecialchars($row['user_email']) ?></td>
                                <td><?= htmlspecialchars($row['user_role']) ?></td>
                                <td>
                                    <!-- Status badge — updated by JS after AJAX call -->
                                    <span class="badge <?= $row['user_is_active'] ? 'badge-active' : 'badge-inactive' ?>"
                                          id="status-badge-<?= $row['user_id'] ?>">
                                        <?= $row['user_is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- This button calls the AJAX function to toggle status -->
                                    <button class="btn btn-sm <?= $row['user_is_active'] ? 'btn-danger' : 'btn-success' ?>"
                                            id="toggle-btn-<?= $row['user_id'] ?>"
                                            onclick="toggleUserStatus(<?= $row['user_id'] ?>)">
                                        <?= $row['user_is_active'] ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center; color:#6b7280; padding:30px;">
                                    No users found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Info note at the bottom -->
            <div class="alert alert-info" style="margin-top:16px;">
                ℹ Click Activate/Deactivate to change user status. Deactivated users will not be able to login.
            </div>
        </div>

    </div>
</div>

<?php include "ConfirmModal.php"; ?>
<script src="../Script/Validation.js"></script>
<script src="../Script/Modal.js"></script>
<script src="../Script/UserManagement.js"></script>
</body>
</html>
