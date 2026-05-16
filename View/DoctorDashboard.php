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
    : '../Assest/Public/Uploads/Doctors/default.png';

$doctorId = $_SESSION['doctor_id'];

// Today's confirmed appointments only
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

if ($todaysAppointments && $todaysAppointments->num_rows > 0) {
    while ($row = $todaysAppointments->fetch_assoc()) {
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
$slotEnd   = strtotime('22:00');
for ($t = $slotStart; $t <= $slotEnd; $t += 1800) {
    $timeSlots[] = date('H:i', $t);
}

$dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
?>
