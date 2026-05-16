<?php

session_start();

include_once "../Config/Db.php";
include_once "../Model/UserModel.php";
include_once "../Model/DoctorModel.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get the values from the login form
    $userEmail    = trim($_POST['user_email']);
    $userPassword = trim($_POST['user_password']);

    // Basic validation
    if (empty($userEmail)) {
        header("Location: ../View/Login.php?error=" . urlencode("Email can not be empty."));
        exit();
    }

    if(str_contains($userEmail, "example.com")){}
    else if(!filter_var($userEmail, FILTER_VALIDATE_EMAIL)){
        header("Location: ../View/Login.php?error=" . urlencode("A valid email is required."));
        exit();
    }

    if(empty($userPassword)){
        header("Location: ../View/Login.php?error=" . urlencode("Password can not be empty."));
        exit();
    }
    else if(preg_match("^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$", $userPassword)){
        header("Location: ../View/Login.php?error=" . urlencode("A valid password is required."));
        exit();
    }

    // Connect to the database
    $db = new Db();
    $conn = $db->connection();
    $userModel = new UserModel();

    // Look up the user by their email
    $user = $userModel->getUserByEmail($conn, $userEmail);

    // If no user found with that email
    if (!$user) {
        header("Location: ../View/Login.php?error=" . urlencode("Invalid email or password."));
        exit();
    }

    // Check if the account has been deactivated by admin
    if ($user['user_is_active'] == 0) {
        header("Location: ../View/Login.php?error=" . urlencode("Your account has been deactivated. Contact us."));
        exit();
    }

    // Check if the password matches the hashed password in the database
    if (!password_verify($userPassword, $user['user_password'])) {
        header("Location: ../View/Login.php?error=" . urlencode("Invalid email or password."));
        exit();
    }

    // Password matched
    $_SESSION['user_id']        = $user['user_id'];
    $_SESSION['user_name']      = $user['user_name'];
    $_SESSION['user_role']      = $user['user_role'];
    $_SESSION['loggedIn']       = true;
    $_SESSION['last_activity']  = time(); // used for session timeout

    // If the user is a doctor, also store their doctor_id in the session
    if ($user['user_role'] == 'Doctor') {
        $doctorModel = new DoctorModel();
        $doctor = $doctorModel->getDoctorByUserId($conn, $user['user_id']);
        if ($doctor) {
            $_SESSION['doctor_id'] = $doctor['doctor_id'];
        }
    }
    // Redirect each role to their own dashboard
    if ($user['user_role'] == 'Admin') {
        header("Location: ../View/AdminDashboard.php");
    } elseif ($user['user_role'] == 'Doctor') {
        header("Location: ../View/DoctorDashboard.php");
    } else {
        // Patient goes to patient home
        header("Location: ../View/PatientHome.php");
    }
    exit();

} 
else {
    header("Location: ../View/Login.php");
    exit();
}
