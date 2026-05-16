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
