<?php
session_start();
include_once "../Config/SessionGuard.php";

if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Doctor') {
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

$doctorProfile  = $doctorModel->getDoctorById($conn, $_SESSION['doctor_id']);
$doctorPhotoSrc = (!empty($doctorProfile['doctor_photo']))
    ? '../' . $doctorProfile['doctor_photo']
    : '../Asset/Public/Uploads/Doctors/default.png';

$doctorId = $_SESSION['doctor_id'];

// Today's confirmed appointments only
$todaysAllAppointments = $appointmentModel->getTodaysConfirmedAppointments($conn, $doctorId);
$todaysAppointments = $appointmentModel->getTodaysConfirmedAppointments($conn, $doctorId);

// Weekly appointments Mon-Fri
$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekEnd   = date('Y-m-d', strtotime('friday this week'));
$weeklyAppointments = $appointmentModel->getWeeklyAppointments($conn, $doctorId, $weekStart, $weekEnd);

// Count stats
$todayCount     = 0;
$completedCount = 0;
$pendingCount   = 0;
$noShowCount    = 0;

// Group weekly appointments by day
$weeklyByDay = ['Mon' => [], 'Tue' => [], 'Wed' => [], 'Thu' => [], 'Fri' => []];

if ($todaysAllAppointments && $todaysAllAppointments->num_rows > 0) {
    while ($row = $todaysAllAppointments->fetch_assoc()) {
        $todayCount++;
        if ($row['appointment_status'] == 'Completed')  $completedCount++;
        elseif ($row['appointment_status'] == 'No-Show') $noShowCount++;
        else $pendingCount++;
    }
}

if ($weeklyAppointments && $weeklyAppointments->num_rows > 0) {
    while ($row = $weeklyAppointments->fetch_assoc()) {
        $dayKey = date('D', strtotime($row['appointment_date']));
        if (isset($weeklyByDay[$dayKey])) {
            $weeklyByDay[$dayKey][] = $row;
        }
    }
}

// Build time slots 09:00 to 22:00 in 30-min steps
$timeSlots = [];
$slotStart = strtotime('09:00');
$slotEnd   = strtotime('18:00');
for ($t = $slotStart; $t <= $slotEnd; $t += 1800) {
    $timeSlots[] = date('H:i', $t);
}

$dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard — CarePlus</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="icon" type="image/x-icon" href="../Asset/Public/favicon.ico">
</head>
<body>
<div style="padding:24px; max-width:1400px; margin:0 auto;">

    <!-- Top bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;
                background:#fff; padding:18px 24px; border-radius:14px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <div>
            <h1 style="font-size:22px; font-weight:700; color:#0d1b2e;">CarePlus — Doctor Panel</h1>
            <p style="font-size:13px; color:#6b7280;">Daily schedule and appointment status control</p>
        </div>
        <div class="docbar-user">
            <img src="<?= htmlspecialchars($doctorPhotoSrc) ?>" class="docbar-photo" alt="Doctor Photo">
            <div>
                <div class="docbar-name"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                <div class="docbar-role">Doctor</div>
            </div>
            <a href="../Controller/LogoutController.php" class="docbar-logout">Logout</a>
        </div>
    </div>

    <!-- Stat cards -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon blue">📅</div>
            <div>
                <div class="stat-value"><?= $todayCount ?></div>
                <div class="stat-label">Today's Confirmed</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">✅</div>
            <div>
                <div class="stat-value"><?= $completedCount ?></div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">⏳</div>
            <div>
                <div class="stat-value"><?= $pendingCount ?></div>
                <div class="stat-label">Remaining</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">❌</div>
            <div>
                <div class="stat-value"><?= $noShowCount ?></div>
                <div class="stat-label">No-Show</div>
            </div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1.3fr 1fr; gap:18px;">

        <!-- Today's confirmed appointments -->
        <div class="card">
            <div class="card-title">Today's Confirmed — <?= date('d M Y') ?></div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>APPT ID</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="todayTableBody">
                        <?php
                        if ($todaysAppointments && $todaysAppointments->num_rows > 0) {
                            $todaysAppointments->data_seek(0);
                            while ($row = $todaysAppointments->fetch_assoc()) {
                                $statusClass = 'badge-' . strtolower(str_replace(['-', ' '], '', $row['appointment_status']));
                                $displayTime = date('h:i A', strtotime($row['appointment_time']));
                                ?>
                                <tr id="appt-row-<?= $row['appointment_id'] ?>">
                                    <td><?= 'APPT-' . str_pad($row['appointment_id'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= $displayTime ?></td>
                                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                                    <td><?= htmlspecialchars(substr($row['appointment_message'], 0, 40)) ?>...</td>
                                    <td>
                                        <span class="badge <?= $statusClass ?>"
                                              id="status-badge-<?= $row['appointment_id'] ?>">
                                            <?= $row['appointment_status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:6px;">
                                            <button class="btn btn-sm btn-success"
                                                    onclick="markCompleted(<?= $row['appointment_id'] ?>)">
                                                ✓ Done
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                    onclick="updateAppointmentStatus(<?= $row['appointment_id'] ?>, 'Doctor', 'No-Show')">
                                                ✗ No-Show
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr id='empty-row'><td colspan='6' style='text-align:center; color:#6b7280; padding:24px;'>No confirmed appointments today.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Weekly schedule grid -->
        <div class="card">
            <div class="card-title">Weekly Schedule — <?= date('d M', strtotime($weekStart)) ?> to <?= date('d M', strtotime($weekEnd)) ?></div>
            <div class="schedule-grid">
                <!-- Header row -->
                <div></div>
                <?php foreach ($dayLabels as $day): ?>
                    <div class="schedule-header"><?= $day ?></div>
                <?php endforeach; ?>

                <?php foreach ($timeSlots as $slot): ?>
                    <div class="schedule-time"><?= date('h:i A', strtotime($slot)) ?></div>

                    <?php foreach ($dayLabels as $day): ?>
                        <div class="schedule-cell">
                            <?php
                            $found = false;
                            if (isset($weeklyByDay[$day])) {
                                foreach ($weeklyByDay[$day] as $weekRow) {
                                    // Match appointment to this 30-min slot
                                    $apptSlot = date('H:i', strtotime($weekRow['appointment_time']));
                                    if ($apptSlot === $slot) {
                                        $found = true;
                                        $apptIdFormatted = 'APPT-' . str_pad($weekRow['appointment_id'], 4, '0', STR_PAD_LEFT);
                                        ?>
                                        <div class="schedule-item"
                                             onclick="showApptModal(
                                                 '<?= $apptIdFormatted ?>',
                                                 '<?= addslashes(htmlspecialchars($weekRow['patient_name'])) ?>',
                                                 '<?= addslashes(htmlspecialchars($weekRow['appointment_message'])) ?>',
                                                 '<?= date('h:i A', strtotime($weekRow['appointment_time'])) ?>',
                                                 '<?= $weekRow['appointment_status'] ?>'
                                             )">
                                            <?= htmlspecialchars($weekRow['patient_name']) ?>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            if (!$found) {
                                echo "<span style='color:#d1d5db; font-size:12px;'>—</span>";
                            }
                            ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<!-- Appointment detail modal -->
<div id="apptDetailBackdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:999;"></div>
<div id="apptDetailModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
     background:#fff; border-radius:14px; padding:28px 32px; z-index:1000; min-width:320px; max-width:460px; box-shadow:0 8px 32px rgba(0,0,0,0.15);">
    <div style="font-size:18px; font-weight:700; color:#0d1b2e; margin-bottom:16px;">Appointment Details</div>
    <table style="width:100%; font-size:14px; border-collapse:collapse;">
        <tr><td style="color:#6b7280; padding:5px 0; width:110px;">ID</td><td id="modalApptId" style="font-weight:600; color:#1a56db;"></td></tr>
        <tr><td style="color:#6b7280; padding:5px 0;">Patient</td><td id="modalApptPatient" style="font-weight:600;"></td></tr>
        <tr><td style="color:#6b7280; padding:5px 0;">Time</td><td id="modalApptTime"></td></tr>
        <tr><td style="color:#6b7280; padding:5px 0;">Status</td><td id="modalApptStatus"></td></tr>
        <tr><td style="color:#6b7280; padding:5px 0; vertical-align:top;">Reason</td>
            <td id="modalApptReason" style="line-height:1.5;"></td></tr>
    </table>
    <div style="margin-top:20px; text-align:right;">
        <button class="btn btn-secondary" onclick="closeApptModal()">Close</button>
    </div>
</div>

<script src="../Script/AppointmentStatus.js"></script>
<script>
// Mark appointment as completed and remove row from today's list
function markCompleted(appointmentId) {
    if (!confirm('Mark this appointment as Completed?')) return;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../Controller/UpdateAppointmentStatusController.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.ok) {
                    var row = document.getElementById('appt-row-' + appointmentId);
                    if (row) row.remove();

                    // Show empty state if no rows left
                    var tbody = document.getElementById('todayTableBody');
                    if (tbody && tbody.querySelectorAll('tr').length === 0) {
                        tbody.innerHTML = "<tr id='empty-row'><td colspan='6' style='text-align:center; color:#6b7280; padding:24px;'>No confirmed appointments today.</td></tr>";
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            } catch(e) {
                alert('Something went wrong.');
            }
        }
    };

    xhr.send('appointment_id=' + appointmentId + '&new_status=Completed');
}

// Show appointment detail modal on weekly grid block click
function showApptModal(id, patient, reason, time, status) {
    document.getElementById('modalApptId').textContent      = id;
    document.getElementById('modalApptPatient').textContent = patient;
    document.getElementById('modalApptTime').textContent    = time;
    document.getElementById('modalApptStatus').textContent  = status;
    document.getElementById('modalApptReason').textContent  = reason;
    document.getElementById('apptDetailModal').style.display   = 'block';
    document.getElementById('apptDetailBackdrop').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// Close appointment detail modal
function closeApptModal() {
    document.getElementById('apptDetailModal').style.display    = 'none';
    document.getElementById('apptDetailBackdrop').style.display = 'none';
    document.body.style.overflow = '';
}

// Close on backdrop click
document.getElementById('apptDetailBackdrop').addEventListener('click', closeApptModal);
</script>
</body>
</html>
