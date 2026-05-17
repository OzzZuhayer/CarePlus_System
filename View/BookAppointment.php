<?php
session_start();
include_once "../Config/SessionGuard.php";

if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Patient') {
    header("Location: Login.php");
    exit();
}

include_once "../Config/Db.php";
include_once "../Model/DoctorModel.php";

$db = new Db();
$conn = $db->connection();
$doctorModel = new DoctorModel();

$doctorId = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;
if ($doctorId <= 0) {
    header("Location: BrowseDoctors.php");
    exit();
}

$doctor = $doctorModel->getDoctorById($conn, $doctorId);
if (!$doctor) {
    header("Location: BrowseDoctors.php");
    exit();
}

// Build next 7 days
$next7Days = [];
for ($i = 0; $i < 7; $i++) {
    $ts = strtotime("+$i days");
    $next7Days[] = [
        'date'    => date('Y-m-d', $ts),
        'dayName' => date('D', $ts),
        'dayNum'  => date('d', $ts),
        'month'   => date('M', $ts),
        'fullDay' => date('l', $ts),
    ];
}

$availableDays = array_map('trim', explode(',', $doctor['doctor_availability']));

$errorMsg = isset($_GET['error']) ? urldecode($_GET['error']) : '';

$photoSrc = !empty($doctor['doctor_photo'])
    ? '../' . $doctor['doctor_photo']
    : '../Asset/Public/Uploads/Doctors/default.png';

$activePage = 'doctors';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment — CarePlus</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="icon" type="image/x-icon" href="../Asset/Public/favicon.ico">
    <style>
        .disabled-day { opacity:0.4; cursor:not-allowed; background:#f3f4f6; }
    </style>
</head>
<body>
<div class="layout">

    <?php include "PatientSidebar.php"; ?>

    <div class="main-content">

        <div class="page-header">
            <h1>Book Appointment</h1>
            <p>Select a date and time slot to book your appointment.</p>
        </div>

        <?php if ($errorMsg): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <div style="display:grid; grid-template-columns:280px 1fr; gap:24px;">

            <!-- Doctor Profile Card -->
            <div>
                <div class="card">
                    <img src="<?= htmlspecialchars($photoSrc) ?>"
                         style="width:100%; height:180px; object-fit:cover; border-radius:8px; margin-bottom:16px;"
                         alt="<?= htmlspecialchars($doctor['user_name']) ?>">

                    <div style="font-size:18px; font-weight:700; color:#0d1b2e;">
                        <?= htmlspecialchars($doctor['user_name']) ?>
                    </div>
                    <div style="color:#1a56db; font-size:13px; margin-top:4px; font-weight:600;">
                        <?= htmlspecialchars($doctor['specialization_name']) ?>
                    </div>
                    <div style="color:#6b7280; font-size:13px; margin-top:6px;">
                        Fee: <strong style="color:#0d1b2e;"><?= htmlspecialchars($doctor['doctor_fee']) ?> TK</strong>
                    </div>

                    <?php if ($doctor['doctor_bio']): ?>
                        <div style="margin-top:14px;">
                            <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">About</div>
                            <p style="font-size:13px; color:#6b7280; line-height:1.6;">
                                <?= htmlspecialchars($doctor['doctor_bio']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <div style="margin-top:14px;">
                        <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:8px;">Available Days</div>
                        <div class="day-pills">
                            <?php foreach ($availableDays as $day): ?>
                                <span class="day-pill"><?= htmlspecialchars($day) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date + Time + Form -->
            <div>
                <div class="card">
                    <div class="card-title">Select Date</div>
                    <div class="date-slots">
                        <?php foreach ($next7Days as $dayInfo):
                            $isAvailable = in_array($dayInfo['fullDay'], $availableDays);
                        ?>
                        <button type="button"
                                class="date-btn <?= !$isAvailable ? 'disabled-day' : '' ?>"
                                data-date="<?= $dayInfo['date'] ?>"
                                <?= !$isAvailable ? 'disabled' : '' ?>
                                onclick="selectDate(this, '<?= $dayInfo['date'] ?>', <?= $doctorId ?>)">
                            <div class="date-day"><?= $dayInfo['dayName'] ?></div>
                            <div class="date-num"><?= $dayInfo['dayNum'] ?></div>
                            <div class="date-month"><?= $dayInfo['month'] ?></div>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <div id="timeSlotSection" style="margin-top:20px; display:none;">
                        <div class="card-title" style="border:none; padding:0; margin-bottom:12px;" id="timeSlotsTitle">
                            Available Time Slots
                        </div>
                        <div id="loadingSlots" style="color:#6b7280; font-size:14px; display:none;">Loading slots...</div>
                        <div class="time-slots" id="timeSlotsContainer"></div>
                    </div>

                    <div id="bookingFormSection" style="margin-top:20px; display:none;">
                        <div class="card-title" style="border:none; padding:0; margin-bottom:12px;">Reason for Visit</div>
                        <form action="../Controller/AppointmentController.php" method="POST" id="bookingForm">
                            <input type="hidden" name="action" value="book_appointment">
                            <input type="hidden" name="doctor_id" value="<?= $doctorId ?>">
                            <input type="hidden" name="appointment_date" id="selectedDate" value="">
                            <input type="hidden" name="appointment_time" id="selectedTime" value="">

                            <div class="form-group" style="margin-bottom:16px;">
                                <label>Selected Date & Time</label>
                                <input type="text" id="selectedDateTimeDisplay" class="form-control" disabled>
                            </div>

                            <div class="form-group" style="margin-bottom:16px;">
                                <label>Describe your problem</label>
                                <textarea name="appointment_message" class="form-control"
                                          placeholder="Please describe your reason for visit..."
                                          maxlength="200" required id="appointmentMessage"></textarea>
                                <small style="color:#9ca3af;" id="charCount">0 / 200</small>
                            </div>

                            <button type="submit" class="btn btn-success" style="width:100%; padding:13px;">
                                📅 Book Appointment
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="../Script/BookingSlots.js"></script>
</body>
</html>
