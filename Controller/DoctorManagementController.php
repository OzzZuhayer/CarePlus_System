<?php

session_start();
include_once "../Config/SessionGuard.php";

include_once "../Config/Db.php";
include_once "../Model/DoctorModel.php";
include_once "../Model/UserModel.php";

// Only admins can manage doctors
if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Admin') {
    header("Location: ../View/Login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $db = new Db();
    $conn = $db->connection();
    $doctorModel = new DoctorModel();
    $userModel = new UserModel();

    $action = $_POST['action'] ?? '';

    // ACTION: Save Doctor (Add or Edit)
    if ($action == 'save_doctor') {

        $doctorId       = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 0;
        $userName       = trim($_POST['user_name']);
        $userEmail      = trim($_POST['user_email']);
        $userPassword   = trim($_POST['user_password']);
        $specializationId = (int)$_POST['specialization_id'];
        $doctorBio      = trim($_POST['doctor_bio']);
        $doctorFee      = trim($_POST['doctor_fee']);

        // Build the availability string from the checked checkboxes
        // e.g. ["Monday", "Wednesday"] becomes "Monday,Wednesday"
        $availabilityDays = isset($_POST['availability_days']) ? $_POST['availability_days'] : [];
        $doctorAvailability = implode(",", $availabilityDays);

        // Validate doctor form inputs
        $errors = [];

        if (empty($userName)) 
            $errors[] = "Doctor name is required.";
        else if(!preg_match("/^[a-zA-Z. ]+$/",$userName))
            $errors[] = "Doctor name can only contain letter, dot, white space.";
        
        if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) 
            $errors[] = "A Valid email is required.";

        if ($doctorId == 0 && empty($userPassword))
            $errors[] = "A valid password is required.";
        elseif (!empty($userPassword) && !preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\S]{8,}$/", $userPassword))
            $errors[] = "Password must contain one letter, one number, one special character. Minimum length is 8.";
        
        if ($specializationId <= 0) 
            $errors[] = "Please select a specialization.";
        if (empty($doctorFee) || !is_numeric($doctorFee) || $doctorFee <= 0) 
            $errors[] = "Valid consultation fee is required.";
        if (empty($availabilityDays)) 
            $errors[] = "Please select at least one availability day.";

        if (!empty($errors)) {
            $errorMsg = implode("|", $errors);
            header("Location: ../View/AdminDoctorManagement.php?error=" . urlencode($errorMsg) . ($doctorId ? "&edit_id=$doctorId" : ""));
            exit();
        }

        // Handle photo upload
        $doctorPhoto = "Assest/Public/Uploads/Doctors/default.png"; // default photo

        if (isset($_FILES['doctor_photo']) && $_FILES['doctor_photo']['error'] == 0) {
            $file     = $_FILES['doctor_photo'];
            $fileExt  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed  = ['jpg', 'jpeg', 'png'];

            // Check file type and size (max 2MB)
            if (!in_array($fileExt, $allowed)) {
                header("Location: ../View/AdminDoctorManagement.php?error=" . urlencode("Only JPG and PNG images are allowed."));
                exit();
            }

            if ($file['size'] > 2 * 1024 * 1024) {
                header("Location: ../View/AdminDoctorManagement.php?error=" . urlencode("Photo must be less than 2MB."));
                exit();
            }

            // Create a unique filename and save the file
            $newFileName = "doctor_" . time() . "." . $fileExt;
            $uploadPath  = "../Assest/Public/Uploads/Doctors/" . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $doctorPhoto = "Assest/Public/Uploads/Doctors/" . $newFileName;
            }
        }       
    }
}