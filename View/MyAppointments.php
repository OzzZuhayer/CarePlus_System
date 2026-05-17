<?php
session_start();
include_once "../Config/SessionGuard.php";

if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Patient') {
    header("Location: Login.php");
    exit();
}

include_once "../Config/Db.php";
include_once "../Model/AppointmentModel.php";

$db = new Db();
$conn = $db->connection();
$appointmentModel = new AppointmentModel();

$patientId = $_SESSION['user_id'];
$appointments = $appointmentModel->getPatientAppointments($conn, $patientId);

$allAppointments = [];
if ($appointments && $appointments->num_rows > 0) {
    while ($row = $appointments->fetch_assoc()) {
        $allAppointments[] = $row;
    }
}

$activePage = 'appointments';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments — CarePlus</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
<div class="layout">

    <?php include "PatientSidebar.php"; ?>

    <div class="main-content">

        <div class="page-header">
            <h1>My Appointments</h1>
            <p>View and manage all your appointments.</p>
        </div>

        <div class="card">
            <div class="tabs">
                <button class="tab-btn active" onclick="filterAppointments('all', this)">All</button>
                <button class="tab-btn" onclick="filterAppointments('pending', this)">Pending</button>
                <button class="tab-btn" onclick="filterAppointments('confirmed', this)">Confirmed</button>
                <button class="tab-btn" onclick="filterAppointments('completed', this)">Completed</button>
                <button class="tab-btn" onclick="filterAppointments('cancelled', this)">Cancelled</button>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>APPT ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Doctor</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Cancellation Note</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="appointmentsTableBody">
                        <?php if (!empty($allAppointments)): ?>
                            <?php foreach ($allAppointments as $row):
                                $statusClass = 'badge-' . strtolower(str_replace(['-',' '], '', $row['appointment_status']));
                                $displayDate = date('d M Y', strtotime($row['appointment_date']));
                                $displayTime = date('h:i A', strtotime($row['appointment_time']));
                                $statusLower = strtolower($row['appointment_status']);
                                $apptId      = 'APPT-' . str_pad($row['appointment_id'], 4, '0', STR_PAD_LEFT);
                                $isCancelled = $row['appointment_status'] === 'Cancelled';

                                // Extract cancellation note from appointment_message if present
                                $cancelNote = '';
                                if ($isCancelled) {
                                    preg_match('/\[Cancelled:\s*(.*?)\]/', $row['appointment_message'], $matches);
                                    $cancelNote = $matches[1] ?? 'Cancelled';
                                }
                            ?>
                            <tr class="appt-row" data-status="<?= $statusLower ?>"
                                id="appt-row-<?= $row['appointment_id'] ?>">
                                <td><?= $apptId ?></td>
                                <td><?= $displayDate ?></td>
                                <td><?= $displayTime ?></td>
                                <td>
                                    <?= htmlspecialchars($row['doctor_name']) ?><br>
                                    <small style="color:#6b7280;"><?= htmlspecialchars($row['specialization_name']) ?></small>
                                </td>
                                <td><?= htmlspecialchars(substr($row['appointment_message'], 0, 60)) ?>...</td>
                                <td>
                                    <span class="badge <?= $statusClass ?>"
                                          id="status-badge-<?= $row['appointment_id'] ?>">
                                        <?= $row['appointment_status'] ?>
                                    </span>
                                </td>
                                <td class="cancel-note-cell">
                                    <?php if ($isCancelled): ?>
                                        <?= htmlspecialchars($cancelNote) ?>
                                    <?php else: ?>
                                        <span style="color:#9ca3af; font-size:13px;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['appointment_status'] === 'Pending'): ?>
                                        <button class="btn btn-sm btn-danger"
                                                onclick="cancelAppointment(<?= $row['appointment_id'] ?>)">
                                            Cancel
                                        </button>
                                    <?php else: ?>
                                        <span style="color:#9ca3af; font-size:13px;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align:center; color:#6b7280; padding:30px;">
                                    No appointments found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top:16px;">
            <a href="BrowseDoctors.php" class="btn btn-primary">+ Book New Appointment</a>
        </div>

    </div>
</div>

<?php include "ConfirmModal.php"; ?>
<script src="../Script/Modal.js"></script>
<script src="../Script/MyAppointments.js"></script>
</body>
</html>
