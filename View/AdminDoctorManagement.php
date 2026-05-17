<?php
session_start();
include_once "../Config/SessionGuard.php";
 
if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Admin') {
    header("Location: Login.php");
    exit();
}
 
include_once "../Config/Db.php";
include_once "../Model/DoctorModel.php";
include_once "../Model/AppointmentModel.php";
 
$db = new Db();
$conn = $db->connection();
$doctorModel = new DoctorModel();
$appointmentModel = new AppointmentModel();
 
// Get all data needed for this page
$doctors         = $doctorModel->getAllDoctors($conn);
$specializations = $doctorModel->getAllSpecializations($conn);
$stats           = $doctorModel->getDoctorStats($conn);
$totalAppts      = $appointmentModel->getTotalAppointmentCount($conn);
$todayAppts      = $appointmentModel->getTodaysAppointmentCount($conn);
 
// Check if we are editing an existing doctor
$editingDoctor = null;
$selectedDays  = [];
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $editingDoctor = $doctorModel->getDoctorById($conn, (int)$_GET['edit_id']);
    if ($editingDoctor) {
        // Convert the comma-separated days string back into an array for the checkboxes
        $selectedDays = array_map('trim', explode(',', $editingDoctor['doctor_availability']));
    }
}
 
$errorMsg   = isset($_GET['error'])   ? urldecode($_GET['error'])   : '';
$successMsg = isset($_GET['success']) ? urldecode($_GET['success']) : '';
 
$activePage = 'doctors';
$allDays    = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Management — CarePlus Admin</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="icon" type="image/x-icon" href="../Asset/Public/favicon.ico">
</head>
<body>
<div class="layout">
 
    <?php include "AdminSidebar.php"; ?>
 
    <div class="main-content">
 
        <div class="page-header">
            <h1>Doctor Management</h1>
            <p>Add, edit or remove doctors and manage their availability.</p>
        </div>
 
        <!-- Messages -->
        <?php if ($errorMsg): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>
        <?php if ($successMsg): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>
 
        <!-- Stat Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon blue">👨‍⚕️</div>
                <div>
                    <div class="stat-value"><?= $stats['total_doctors'] ?? 0 ?></div>
                    <div class="stat-label">Total Enrolled Doctors</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">✅</div>
                <div>
                    <div class="stat-value"><?= $stats['active_doctors'] ?? 0 ?></div>
                    <div class="stat-label">Active Doctors</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">📅</div>
                <div>
                    <div class="stat-value"><?= $totalAppts ?></div>
                    <div class="stat-label">Total Appointments</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">🕐</div>
                <div>
                    <div class="stat-value"><?= $todayAppts ?></div>
                    <div class="stat-label">Today's Appointments</div>
                </div>
            </div>
        </div>
 
        <!-- Add / Edit Doctor Form -->
        <div class="card">
            <div class="card-title"><?= $editingDoctor ? 'Edit Doctor' : 'Add / Edit Doctor' ?></div>
 
            <form action="../Controller/DoctorManagementController.php" method="POST"
                  enctype="multipart/form-data" id="doctorForm">
                <input type="hidden" name="action" value="save_doctor">
                <input type="hidden" name="doctor_id" value="<?= $editingDoctor['doctor_id'] ?? 0 ?>">
 
                <div class="form-grid">
                    <!-- Left Column -->
                    <div>
                        <div class="form-group" style="margin-bottom:14px;">
                            <label>Doctor Name</label>
                            <input type="text" name="user_name" class="form-control"
                                   placeholder="Enter doctor name"
                                   value="<?= htmlspecialchars($editingDoctor['user_name'] ?? '') ?>" required>
                        </div>
 
                        <div class="form-group" style="margin-bottom:14px;">
                            <label>Email</label>
                            <input type="email" name="user_email" class="form-control"
                                   placeholder="Enter email address"
                                   value="<?= htmlspecialchars($editingDoctor['user_email'] ?? '') ?>" required>
                        </div>
 
                        <div class="form-group" style="margin-bottom:14px;">
                            <label>Password <?= $editingDoctor ? '(leave blank to keep current)' : '' ?></label>
                            <input type="password" name="user_password" class="form-control"
                                   placeholder="Enter password" <?= $editingDoctor ? '' : 'required' ?>>
                        </div>
 
                        <div class="form-group" style="margin-bottom:14px;">
                            <label>Specialization</label>
                            <select name="specialization_id" class="form-control" required>
                                <option value="">Select specialization</option>
                                <?php
                                // We need to loop through specializations — reset the pointer first
                                if ($specializations) {
                                    $specializations->data_seek(0);
                                    while ($spec = $specializations->fetch_assoc()) {
                                        $selected = (isset($editingDoctor['specialization_id']) &&
                                                     $editingDoctor['specialization_id'] == $spec['specialization_id'])
                                                    ? 'selected' : '';
                                        echo "<option value=\"{$spec['specialization_id']}\" $selected>{$spec['specialization_name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
 
                        <div class="form-group" style="margin-bottom:14px;">
                            <label>Consultation Fee (TK)</label>
                            <input type="number" name="doctor_fee" class="form-control"
                                   placeholder="Enter fee amount" min="1" step="0.01"
                                   value="<?= htmlspecialchars($editingDoctor['doctor_fee'] ?? '') ?>" required>
                        </div>
                    </div>
 
                    <!-- Right Column -->
                    <div>
                        <!-- Photo Upload Box -->
                        <div class="form-group" style="margin-bottom:14px;">
                            <label>Upload Photo</label>
                            <div class="photo-upload-box" onclick="document.getElementById('photoInput').click()">
                                <input type="file" id="photoInput" name="doctor_photo" accept=".jpg,.jpeg,.png">
                                <div class="photo-upload-icon">☁</div>
                                <div class="photo-upload-text">
                                    Click to upload photo<br>
                                    <small>JPG, PNG (Max 2MB)</small>
                                </div>
                                <div id="photoName" style="margin-top:8px; font-size:13px; color:#1a56db;"></div>
                            </div>
                            <?php if ($editingDoctor && $editingDoctor['doctor_photo']): ?>
                                <small style="color:#6b7280;">Current photo will be kept if no new one is uploaded.</small>
                            <?php endif; ?>
                        </div>
 
                        <div class="form-group" style="margin-bottom:14px;">
                            <label>Short Bio</label>
                            <textarea name="doctor_bio" class="form-control"
                                      placeholder="Write about doctor... (max 300 characters)"
                                      maxlength="300"><?= htmlspecialchars($editingDoctor['doctor_bio'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
 
                <!-- Availability Days Checkboxes (full width) -->
                <div class="form-group" style="margin-top:10px;">
                    <label>Availability Days (Weekly)</label>
                    <div class="checkbox-group">
                        <?php foreach ($allDays as $day): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="availability_days[]" value="<?= $day ?>"
                                       <?= in_array($day, $selectedDays) ? 'checked' : '' ?>>
                                <?= $day ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
 
                <div class="form-actions">
                    <?php if ($editingDoctor): ?>
                        <a href="AdminDoctorManagement.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                    <button type="button" class="btn btn-secondary" onclick="resetDoctorForm()">Reset</button>
                    <button type="submit" class="btn btn-success">
                        <?= $editingDoctor ? 'Update Doctor' : 'Save Doctor' ?>
                    </button>
                </div>
            </form>
        </div>
 
        <!-- Doctor List Table -->
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <div class="card-title" style="margin-bottom:0; border:none; padding:0;">Doctor List</div>
                <input type="text" id="doctorSearch" class="form-control"
                       placeholder="Search doctor..." style="max-width:220px;"
                       onkeyup="searchTable('doctorSearch', 'doctorTable')">
            </div>
 
            <div class="table-wrap">
                <table id="doctorTable">
                    <thead>
                        <tr>
                            <th>DOC ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Specialization</th>
                            <th>Fee (TK)</th>
                            <th>Available Days</th>
                            <th>Total Appointments</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $counter = 1;
                        if ($doctors && $doctors->num_rows > 0) {
                            while ($row = $doctors->fetch_assoc()) {
                                $statusBadge = $row['user_is_active']
                                    ? '<span class="badge badge-active">Active</span>'
                                    : '<span class="badge badge-inactive">Inactive</span>';
                                $photoSrc = !empty($row['doctor_photo'])
                                    ? '../' . $row['doctor_photo']
                                    : '../Asset/Public/Uploads/Doctors/default.png';
                                ?>
                                <tr>
                                    <td><?= 'DOC-' . str_pad($row['doctor_id'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td>
                                        <img src="<?= htmlspecialchars($photoSrc) ?>"
                                             class="doctor-avatar" alt="Photo">
                                    </td>
                                    <td><strong><?= htmlspecialchars($row['user_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['specialization_name']) ?></td>
                                    <td><?= htmlspecialchars($row['doctor_fee']) ?></td>
                                    <td><?= htmlspecialchars($row['doctor_availability']) ?></td>
                                    <td style='text-align:center; font-weight:600; color:#1a56db;'><?= $row['total_appointments'] ?></td>
                                    <td><?= $statusBadge ?></td>
                                    <td>
                                        <div id="doc-actions-<?= $row['doctor_id'] ?>">
                                            <!-- initial single edit button; clicking expands to Edit + Delete -->
                                            <a href="?edit_id=<?= $row['doctor_id'] ?>"
                                               class="btn btn-sm btn-primary btn-icon" title="Edit"
                                               onclick="return expandDocActions(event, <?= $row['doctor_id'] ?>)">✏</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align:center; color:#6b7280; padding:30px;'>No doctors found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
 
    </div>
</div>
 
<?php include "ConfirmModal.php"; ?>
<script src="../Script/DoctorManagement.js"></script>
<script src="../Script/Modal.js"></script>
<script>
// Expand the actions area to show Edit and Delete buttons
function expandDocActions(e, doctorId) {
    e.preventDefault();
    var container = document.getElementById('doc-actions-' + doctorId);
    if (!container) return false;
 
    // If already expanded, do nothing
    if (container.dataset.expanded === '1') return false;
 
    var editUrl = '?edit_id=' + doctorId;
    container.innerHTML = '' +
        '<button class="btn btn-sm btn-primary" onclick="window.location=\'' + editUrl + '\'">Edit</button> ' +
        '<button class="btn btn-sm btn-danger" onclick="confirmDeleteDoctor(' + doctorId + ')">Delete</button>' +
        ' <button class="btn btn-sm btn-secondary" onclick="collapseDocActions(' + doctorId + ')">Cancel</button>';
    container.dataset.expanded = '1';
    return false;
}
 
function collapseDocActions(doctorId) {
    var container = document.getElementById('doc-actions-' + doctorId);
    if (!container) return;
    var link = '<a href="?edit_id=' + doctorId + '" class="btn btn-sm btn-primary btn-icon" title="Edit" onclick="return expandDocActions(event, ' + doctorId + ')">✏</a>';
    container.innerHTML = link;
    container.dataset.expanded = '0';
}
 
function confirmDeleteDoctor(doctorId) {
    if (typeof showConfirmModal === 'function') {
        showConfirmModal(function() { submitDeleteDoctorForm(doctorId); }, 'Delete this doctor?', 'delete');
    } else {
        if (confirm('Delete this doctor?')) submitDeleteDoctorForm(doctorId);
    }
}
 
function submitDeleteDoctorForm(doctorId) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '../Controller/DoctorManagementController.php';
 
    var a1 = document.createElement('input');
    a1.type = 'hidden'; a1.name = 'action'; a1.value = 'delete_doctor';
    var a2 = document.createElement('input');
    a2.type = 'hidden'; a2.name = 'doctor_id'; a2.value = doctorId;
 
    form.appendChild(a1);
    form.appendChild(a2);
    document.body.appendChild(form);
    form.submit();
}
</script>
</body>
</html>