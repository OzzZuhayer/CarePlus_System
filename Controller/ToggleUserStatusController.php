<?php

session_start();

include_once "../Config/Db.php";
include_once "../Model/UserModel.php";

// This endpoint is called via AJAX — it returns JSON
header('Content-Type: application/json');

// Only admins can access this
if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

    if ($userId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user ID.']);
        exit();
    }

    $db = new Db();
    $conn = $db->connection();
    $userModel = new UserModel();

    // Get the user's current status
    $currentStatus = $userModel->getUserStatus($conn, $userId);

    if ($currentStatus === null) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        exit();
    }

    // Flip the status: if active (1) make inactive (0), if inactive (0) make active (1)
    $newStatus = $currentStatus == 1 ? 0 : 1;

    $success = $userModel->toggleUserStatus($conn, $userId, $newStatus);

    if ($success) {
        $statusLabel = $newStatus == 1 ? 'Active' : 'Inactive';
        echo json_encode([
            'status'     => 'success',
            'new_status' => $newStatus,
            'label'      => $statusLabel
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update status.']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
exit();
