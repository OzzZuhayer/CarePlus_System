<?php
session_start();
include_once "../Config/SessionGuard.php";

if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Admin') {
    header("Location: Login.php");
    exit();
}

include_once "../Config/Db.php";
include_once "../Model/AppointmentModel.php";
include_once "../Model/DoctorModel.php";

$db = new Db();
$conn = $db->connection();
$appointmentModel = new AppointmentModel();
$doctorModel      = new DoctorModel();

// Read GET filters
$filterDoctorId = isset($_GET['doctor_id'])  ? (int)$_GET['doctor_id']        : 0;
$filterDate     = isset($_GET['filter_date']) ? trim($_GET['filter_date'])      : '';
$filterStatus   = isset($_GET['status'])      ? trim($_GET['status'])           : '';

// Get filtered appointments
$appointments = $appointmentModel->getFilteredAppointments($conn, $filterDoctorId, $filterDate, $filterStatus);

// Get all doctors for the filter dropdown
$allDoctors = $doctorModel->getAllDoctors($conn);

$activePage = 'appointments';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments — CarePlus Admin</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="icon" type="image/x-icon" href="../Asset/Public/favicon.ico">
</head>
<body>
<div class="layout">

    <?php include "AdminSidebar.php"; ?>

    <div class="main-content">

        <div class="page-header">
            <h1>Appointment Management</h1>
            <p>Filter, view and manage all appointments.</p>
        </div>

        <!-- Filters -->
        <div class="card" style="margin-bottom:16px;">
            <form method="GET" action="AdminAppointments.php">
                <div style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">

                    <div class="form-group" style="flex:1; min-width:160px;">
                        <label>Doctor</label>
                        <select name="doctor_id" class="form-control">
                            <option value="0">All Doctors</option>
                            <?php
                            if ($allDoctors && $allDoctors->num_rows > 0) {
                                while ($doc = $allDoctors->fetch_assoc()) {
                                    $sel = ($filterDoctorId == $doc['doctor_id']) ? 'selected' : '';
                                    echo "<option value='{$doc['doctor_id']}' $sel>{$doc['user_name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group" style="flex:1; min-width:160px;">
                        <label>Date</label>
                        <input type="date" name="filter_date" class="form-control"
                               value="<?= htmlspecialchars($filterDate) ?>">
                    </div>

                    <div class="form-group" style="flex:1; min-width:160px;">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            <?php
                            $statuses = ['Pending','Confirmed','Completed','Cancelled','No-Show'];
                            foreach ($statuses as $s) {
                                $sel = ($filterStatus === $s) ? 'selected' : '';
                                echo "<option value='$s' $sel>$s</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div style="display:flex; gap:8px;">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="AdminAppointments.php" class="btn btn-secondary">Reset</a>
                    </div>

                </div>
            </form>
        </div>

        <!-- Appointments Table -->
        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>APPT ID</th>
                            <th>Date & Time</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($appointments && $appointments->num_rows > 0) {
                            while ($row = $appointments->fetch_assoc()) {
                                $statusClass = 'badge-' . strtolower(str_replace(['-',' '], '', $row['appointment_status']));
                                $displayDate = date('d M Y', strtotime($row['appointment_date']));
                                $displayTime = date('h:i A', strtotime($row['appointment_time']));
                                ?>
                                <tr id="appt-row-<?= $row['appointment_id'] ?>">
                                    <td><?= 'APPT-' . str_pad($row['appointment_id'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= $displayDate ?><br><small style="color:#6b7280;"><?= $displayTime ?></small></td>
                                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($row['doctor_name']) ?><br>
                                        <small style="color:#6b7280;"><?= htmlspecialchars($row['specialization_name']) ?></small>
                                    </td>
                                    <td style="max-width:150px;"><?= htmlspecialchars(substr($row['appointment_message'], 0, 60)) ?>...</td>
                                    <td>
                                        <span class="badge <?= $statusClass ?>"
                                              id="status-badge-<?= $row['appointment_id'] ?>">
                                            <?= $row['appointment_status'] ?>
                                        </span>
                                    </td>
                                    <td id="appt-actions-<?= $row['appointment_id'] ?>">
                                        <?php if ($row['appointment_status'] === 'Cancelled'): ?>
                                            <span style="color:#9ca3af; font-size:13px;">—</span>
                                        <?php else: ?>
                                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                                <?php if ($row['appointment_status'] === 'Pending'): ?>
                                                    <!-- Confirm pending appointment -->
                                                    <button class="btn btn-sm btn-success"
                                                            onclick="updateAppointmentStatus(<?= $row['appointment_id'] ?>, 'Admin', 'Confirmed')">
                                                        Confirm
                                                    </button>
                                                <?php endif; ?>
                                                <!-- Cancel with required reason -->
                                                <button class="btn btn-sm btn-danger"
                                                        onclick="cancelWithReason(<?= $row['appointment_id'] ?>)">
                                                    Cancel
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='7' style='text-align:center;color:#6b7280;padding:30px;'>No appointments found.</td></tr>";
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
<script src="../Script/AppointmentStatus.js"></script>
</body>
</html>
