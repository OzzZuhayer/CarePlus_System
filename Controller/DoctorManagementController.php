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
        $doctorPhoto = "Asset/Public/Uploads/Doctors/default.png"; // default photo

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
            $uploadPath  = "../Asset/Public/Uploads/Doctors/" . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $doctorPhoto = "Asset/Public/Uploads/Doctors/" . $newFileName;
            }
        }

        // Add new doctor
        if ($doctorId == 0) {

            // Check if email already exists
            if ($userModel->emailExists($conn, $userEmail)) {
                header("Location: ../View/AdminDoctorManagement.php?error=" . urlencode("This email is already registered."));
                exit();
            }

            // Hash the password
            $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

            // First create the user account
            $newUserId = $doctorModel->createDoctorUser($conn, $userName, $userEmail, $hashedPassword);

            if (!$newUserId) {
                header("Location: ../View/AdminDoctorManagement.php?error=" . urlencode("Failed to create doctor account."));
                exit();
            }

            // Then create the doctor profile
            $success = $doctorModel->createDoctorProfile($conn, $newUserId, $specializationId, $doctorBio, $doctorFee, $doctorPhoto, $doctorAvailability);

            if ($success) {
                header("Location: ../View/AdminDoctorManagement.php?success=" . urlencode("Doctor added successfully!"));
            } else {
                header("Location: ../View/AdminDoctorManagement.php?error=" . urlencode("Failed to save doctor profile."));
            }
            exit();
        }

        // Update existing doctor
        if ($doctorId > 0) {

            // Get the existing doctor data
            $existingDoctor = $doctorModel->getDoctorById($conn, $doctorId);

            if (!$existingDoctor) {
                header("Location: ../View/AdminDoctorManagement.php?error=" . urlencode("Doctor not found."));
                exit();
            }

            $existingUserId = $existingDoctor['user_id'];

            // Check email is not taken by someone else
            if ($doctorModel->emailExistsForOtherUser($conn, $userEmail, $existingUserId)) {
                header("Location: ../View/AdminDoctorManagement.php?error=" . urlencode("This email is already used by another user.") . "&edit_id=$doctorId");
                exit();
            }

            // If no new photo was uploaded, keep the existing one
            if ($doctorPhoto == "Asset/Public/Uploads/Doctors/default.png" && !empty($existingDoctor['doctor_photo'])) {
                $doctorPhoto = $existingDoctor['doctor_photo'];
            }

            // If a new password was entered, hash it — otherwise keep the old one
            if (!empty($userPassword)) {
                $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);
            } else {
                // Get the existing hashed password from the database
                $hashedPassword = $userModel->getUserPassword($conn, $existingUserId);
            }

            // Update the user account info
            $doctorModel->updateDoctorUser($conn, $existingUserId, $userName, $userEmail, $hashedPassword);

            // Update the doctor profile info
            $success = $doctorModel->updateDoctorProfile($conn, $doctorId, $specializationId, $doctorBio, $doctorFee, $doctorPhoto, $doctorAvailability);

            if ($success) {
                header("Location: ../View/AdminDoctorManagement.php?success=" . urlencode("Doctor updated successfully!"));
            } else {
                header("Location: ../View/AdminDoctorManagement.php?error=" . urlencode("Failed to update doctor."));
            }
            exit();
        }
    }

    // ACTION: Delete Doctor
    if ($action == 'delete_doctor') {

        $doctorId = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 0;

        if ($doctorId <= 0) {
            header("Location: ../View/AdminDoctorManagement.php?error=" . urlencode("Invalid doctor."));
            exit();
        }

        $doctor = $doctorModel->getDoctorById($conn, $doctorId);

        if (!$doctor) {
            header("Location: ../View/AdminDoctorManagement.php?error=" . urlencode("Doctor not found."));
            exit();
        }

        // Deactivate instead of hard delete (to preserve appointment history)
        $success = $doctorModel->deactivateDoctor($conn, $doctor['user_id']);

        if ($success) {
            header("Location: ../View/AdminDoctorManagement.php?success=" . urlencode("Doctor deactivated successfully."));
        } else {
            header("Location: ../View/AdminDoctorManagement.php?error=" . urlencode("Failed to deactivate doctor."));
        }
        exit();
    }

    // ACTION: Save Specialization (Add or Edit)
    if ($action == 'save_specialization') {

        $specializationId   = isset($_POST['specialization_id']) ? (int)$_POST['specialization_id'] : 0;
        $specializationName = trim($_POST['specialization_name']);

        if (empty($specializationName)) {
            header("Location: ../View/AdminSpecializations.php?error=" . urlencode("Specialization name is required."));
            exit();
        }

        // Check for duplicate name
        if ($doctorModel->specializationExists($conn, $specializationName, $specializationId)) {
            header("Location: ../View/AdminSpecializations.php?error=" . urlencode("This specialization already exists."));
            exit();
        }

        if ($specializationId > 0) {
            // Update existing
            $success = $doctorModel->updateSpecialization($conn, $specializationId, $specializationName);
            $msg = "Specialization updated successfully!";
        } else {
            // Add new
            $success = $doctorModel->addSpecialization($conn, $specializationName);
            $msg = "Specialization added successfully!";
        }

        if ($success) {
            header("Location: ../View/AdminSpecializations.php?success=" . urlencode($msg));
        } else {
            header("Location: ../View/AdminSpecializations.php?error=" . urlencode("Failed to save specialization."));
        }
        exit();
    }

    // ACTION: Delete Specialization
    if ($action == 'delete_specialization') {

        $specializationId = isset($_POST['specialization_id']) ? (int)$_POST['specialization_id'] : 0;

        if ($specializationId <= 0) {
            header("Location: ../View/AdminSpecializations.php?error=" . urlencode("Invalid specialization."));
            exit();
        }

        // Cannot delete if doctors are assigned to it
        if ($doctorModel->specializationInUse($conn, $specializationId)) {
            header("Location: ../View/AdminSpecializations.php?error=" . urlencode("Cannot delete — doctors are assigned to this specialization."));
            exit();
        }

        $success = $doctorModel->deleteSpecialization($conn, $specializationId);

        if ($success) {
            header("Location: ../View/AdminSpecializations.php?success=" . urlencode("Specialization deleted successfully."));
        } else {
            header("Location: ../View/AdminSpecializations.php?error=" . urlencode("Failed to delete specialization."));
        }
        exit();
    }

} else {
    header("Location: ../View/AdminDoctorManagement.php");
    exit();
}
