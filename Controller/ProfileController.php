<?php

session_start();
include_once "../Config/SessionGuard.php";

include_once "../Config/Db.php";
include_once "../Model/UserModel.php";

// Redirect if not logged in
if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    header("Location: ../View/Login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $db = new Db();
    $conn = $db->connection();
    $userModel = new UserModel();

    $userId = $_SESSION['user_id'];
    $action = $_POST['action'] ?? '';

    // Update profile info
    if ($action == 'update_profile') {

        $userName  = trim($_POST['user_name']);
        $userDob   = trim($_POST['user_dob']);
        $userBg    = trim($_POST['user_bg']);
        $userPhone = trim($_POST['user_phone']);

        $errors = [];

        if (empty($userName))
            $errors[] = "Full name is required.";
        elseif (!preg_match("/^[a-zA-Z ]+$/", $userName))
            $errors[] = "Full name can only contain letters and white space.";

        if (empty($userDob))
            $errors[] = "Date of birth is required.";

        if (empty($userBg))
            $errors[] = "Blood group is required.";

        if (empty($userPhone))
            $errors[] = "Phone number is required.";
        elseif (!preg_match("/^01[3-9]\d{8}$/", $userPhone))
            $errors[] = "A valid phone number is required.";

        if (!empty($errors)) {
            header("Location: ../View/EditProfile.php?error=" . urlencode(implode("|", $errors)));
            exit();
        }

        $success = $userModel->updateProfile($conn, $userId, $userName, $userDob, $userBg, $userPhone);

        if ($success) {
            $_SESSION['user_name'] = $userName;
            header("Location: ../View/EditProfile.php?success=" . urlencode("Profile updated successfully."));
        } else {
            header("Location: ../View/EditProfile.php?error=" . urlencode("Failed to update profile. Please try again."));
        }
        exit();
    }
