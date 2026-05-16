<?php
session_start();
include_once "../Config/Db.php";
include_once "../Model/AppointmentModel.php";

header('Content-Type: application/json');

if (!isset($_SESSION['loggedIn']) || !in_array($_SESSION['user_role'], ['Doctor', 'Admin'])) {
    echo json_encode(['ok' => false, 'message' => 'Unauthorized.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $appointmentId = (int)($_POST['appointment_id'] ?? 0);
    $newStatus     = trim($_POST['new_status'] ?? '');
    $reason        = trim($_POST['reason'] ?? '');
    $action        = trim($_POST['action'] ?? 'update_status');

    $allowedStatuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled', 'No-Show'];

    if ($appointmentId <= 0) {
        echo json_encode(['ok' => false, 'message' => 'Invalid appointment.']);
        exit();
    }

    $db = new Db();
    $conn = $db->connection();
    $appointmentModel = new AppointmentModel();

    // Cancel with reason (admin only)
    if ($action === 'cancel_with_reason') {
        if ($_SESSION['user_role'] !== 'Admin') {
            echo json_encode(['ok' => false, 'message' => 'Unauthorized.']);
            exit();
        }
        if (empty($reason)) {
            echo json_encode(['ok' => false, 'message' => 'Cancellation reason is required.']);
            exit();
        }
        $success = $appointmentModel->cancelWithReason($conn, $appointmentId, $reason);
        echo json_encode(['ok' => $success, 'new_status' => 'Cancelled']);
        exit();
    }

    // Regular status update
    if (!in_array($newStatus, $allowedStatuses)) {
        echo json_encode(['ok' => false, 'message' => 'Invalid status.']);
        exit();
    }

    if ($_SESSION['user_role'] == 'Doctor') {
        if (!in_array($newStatus, ['Completed', 'No-Show'])) {
            echo json_encode(['ok' => false, 'message' => 'Doctors can only mark Completed or No-Show.']);
            exit();
        }
    }

    $success = $appointmentModel->updateAppointmentStatus($conn, $appointmentId, $newStatus);
    echo json_encode(['ok' => $success, 'new_status' => $newStatus]);

} else {
    echo json_encode(['ok' => false, 'message' => 'Invalid request.']);
}
exit();
