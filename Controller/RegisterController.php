<?php
session_start();
include_once "../Config/Db.php";
include_once "../Model/UserModel.php";
// Only run this code if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get all the values the user typed in the form
    $userName     = trim($_POST['user_name']);
    $userEmail    = trim($_POST['user_email']);
    $userPassword = trim($_POST['user_password']);
    $userDob      = trim($_POST['user_dob']);
    $userBg       = trim($_POST['user_bg']);
    $userPhone    = trim($_POST['user_phone']);

    //  Server-side Validation 
    $errors = [];

    if (empty($userName)) {
        $errors[] = "Full name is required.";
    }
    elseif(!preg_match("/^[a-zA-Z ]+$/",$userName)){
        $errors[] = "Full name can only contain letter and white space.";
    }

    if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }

    if (empty($userPassword)) {
        $errors[] = "Password is required.";
    }
    else if(!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\S]{8,}$/", $userPassword)){
        $errors[] = "Password must contain one letter, one number, one special character.";
    }

    if (empty($userDob)) {
        $errors[] = "Date of birth is required.";
    }

    if (empty($userBg)) {
        $errors[] = "Blood group is required.";
    }

    if (empty($userPhone)) {
        $errors[] = "Phone number is required.";
    }
    elseif(!preg_match("/^01[3-9]\d{8}$/", $userPhone)){
        $errors[] = "A valid phone number is required.";
    }

    // If there are validation errors, go back to registration with error messages
    if (!empty($errors)) {
        $errorMessage = implode("|", $errors);
        header("Location: ../View/Register.php?error=" . urlencode($errorMessage));
        exit();
    }

    // Connect to the database
    $db = new Db();
    $conn = $db->connection();
    $userModel = new UserModel();

    // Check if this email is already registered
    if ($userModel->emailExists($conn, $userEmail)) {
        header("Location: ../View/Register.php?error=" . urlencode("This email is already registered. Please login."));
        exit();
    }

    // Hash the password before saving
    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

    // Save the new patient to the database
    $success = $userModel->registerPatient($conn, $userName, $userEmail, $hashedPassword, $userDob, $userBg, $userPhone);

    if ($success) {
        // Registration worked
        header("Location: ../View/Login.php?success=" . urlencode("Registration successful! Please login."));
        exit();
    } else {
        // Something went wrong with the database
        header("Location: ../View/Register.php?error=" . urlencode("Registration failed. Please try again."));
        exit();
    }

}
else {
    // If someone tries to access controller file directly, redirect
    header("Location: ../View/Register.php");
    exit();
}
