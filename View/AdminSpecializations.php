<?php
session_start();
include_once "../Config/SessionGuard.php";

if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Admin') {
    header("Location: Login.php");
    exit();
}

include_once "../Config/Db.php";
include_once "../Model/DoctorModel.php";

$db = new Db();
$conn = $db->connection();
$doctorModel = new DoctorModel();

$specializations = $doctorModel->getAllSpecializations($conn);

// Check if editing a specialization
$editingSpec = null;
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $editingSpec = $doctorModel->getSpecializationById($conn, (int)$_GET['edit_id']);
}

$errorMsg   = isset($_GET['error'])   ? urldecode($_GET['error'])   : '';
$successMsg = isset($_GET['success']) ? urldecode($_GET['success']) : '';

$activePage = 'specializations';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specializations — CarePlus Admin</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
<div class="layout">

    <?php include "AdminSidebar.php"; ?>

    <div class="main-content">

        <div class="page-header">
            <h1>Specializations</h1>
            <p>Manage medical specializations available in the system.</p>
        </div>

        <?php if ($errorMsg): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>
        <?php if ($successMsg): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>

        <!-- Add / Edit Specialization Form -->
        <div class="card">
            <div class="card-title"><?= $editingSpec ? 'Edit Specialization' : 'Add Specialization' ?></div>

            <form action="../Controller/DoctorManagementController.php" method="POST">
                <input type="hidden" name="action" value="save_specialization">
                <input type="hidden" name="specialization_id" value="<?= $editingSpec['specialization_id'] ?? 0 ?>">

                <div style="display:flex; gap:12px; align-items:flex-end;">
                    <div class="form-group" style="flex:1;">
                        <label>Specialization Name</label>
                        <input type="text" name="specialization_name" class="form-control"
                               placeholder="e.g. Cardiology"
                               value="<?= htmlspecialchars($editingSpec['specialization_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-actions" style="margin-top:0;">
                        <?php if ($editingSpec): ?>
                            <a href="AdminSpecializations.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary">
                            <?= $editingSpec ? 'Update' : 'Add Specialization' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Specializations List -->
        <div class="card">
            <div class="card-title">All Specializations</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Specialization Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($specializations && $specializations->num_rows > 0) {
                            while ($row = $specializations->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['specialization_name']) . "</td>";
                                echo "<td>";
                                echo "<div style='display:flex; gap:6px;'>";
                                // Edit button
                                echo "<a href='?edit_id={$row['specialization_id']}' class='btn btn-sm btn-primary btn-icon' title='Edit'>✏</a>";
                                // Delete form
                                echo "<form action='../Controller/DoctorManagementController.php' method='POST'
                                           id='deleteSpecForm{$row['specialization_id']}'>";
                                echo "<input type='hidden' name='action' value='delete_specialization'>";
                                echo "<input type='hidden' name='specialization_id' value='{$row['specialization_id']}'>";
                                echo "<button type='button' class='btn btn-sm btn-danger btn-icon' title='Delete'
                                             onclick=\"showConfirmModal(document.getElementById('deleteSpecForm{$row['specialization_id']}'), 'Delete specialization: {$row['specialization_name']}?', 'delete')\">🗑</button>";
                                echo "</form>";
                                echo "</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center; color:#6b7280; padding:30px;'>No specializations found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include "ConfirmModal.php"; ?>
<script src="../Script/Modal.js"></script>
</body>
</html>
