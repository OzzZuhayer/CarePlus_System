<?php

// This file contains all database operations related to doctors and specializations.

class DoctorModel {

    // SPECIALIZATIONS

    // Get all specializations (used in dropdowns)
    function getAllSpecializations($conn) {
        $sql = "SELECT specialization_id, specialization_name FROM specializations ORDER BY specialization_name ASC";
        $result = $conn->query($sql);
        return $result;
    }

    // Get a single specialization by ID
    function getSpecializationById($conn, $specializationId) {
        $sql = "SELECT specialization_id, specialization_name FROM specializations WHERE specialization_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $specializationId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row;
    }

    // Add a new specialization
    function addSpecialization($conn, $specializationName) {
        $sql = "INSERT INTO specializations (specialization_name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $specializationName);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Check if specialization name already exists
    function specializationExists($conn, $specializationName, $excludeId = 0) {
        $sql = "SELECT specialization_id FROM specializations WHERE specialization_name = ? AND specialization_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $specializationName, $excludeId);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Update a specialization name
    function updateSpecialization($conn, $specializationId, $specializationName) {
        $sql = "UPDATE specializations SET specialization_name = ? WHERE specialization_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $specializationName, $specializationId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Delete a specialization (only if no doctors are assigned to it)
    function deleteSpecialization($conn, $specializationId) {
        $sql = "DELETE FROM specializations WHERE specialization_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $specializationId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Check if any doctor is using this specialization
    function specializationInUse($conn, $specializationId) {
        $sql = "SELECT doctor_id FROM doctors WHERE specialization_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $specializationId);
        $stmt->execute();
        $stmt->store_result();
        $inUse = $stmt->num_rows > 0;
        $stmt->close();
        return $inUse;
    }

    // DOCTORS

    // Get all doctors with their user info, specialization name, and total appointment count
    function getAllDoctors($conn) {
        $sql = "SELECT d.doctor_id, d.doctor_fee, d.doctor_availability, d.doctor_photo,
                       u.user_id, u.user_name, u.user_email, u.user_is_active,
                       s.specialization_name,
                       COUNT(a.appointment_id) AS total_appointments
                FROM doctors d
                JOIN users u ON d.user_id = u.user_id
                JOIN specializations s ON d.specialization_id = s.specialization_id
                LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
                GROUP BY d.doctor_id, u.user_id, s.specialization_id
                ORDER BY d.doctor_id ASC";
        $result = $conn->query($sql);
        return $result;
    }

    // Get a single doctor by doctor_id (with user and specialization info)
    function getDoctorById($conn, $doctorId) {
        $sql = "SELECT d.doctor_id, d.doctor_bio, d.doctor_fee, d.doctor_availability, d.doctor_photo, d.specialization_id,
                       u.user_id, u.user_name, u.user_email, u.user_is_active,
                       s.specialization_name
                FROM doctors d
                JOIN users u ON d.user_id = u.user_id
                JOIN specializations s ON d.specialization_id = s.specialization_id
                WHERE d.doctor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        $stmt->close();
        return $doctor;
    }

    // Get doctor by user_id (used when doctor logs in)
    function getDoctorByUserId($conn, $userId) {
        $sql = "SELECT doctor_id FROM doctors WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row;
    }

    // Create a new user account for the doctor
    function createDoctorUser($conn, $userName, $userEmail, $userPassword) {
        $sql = "INSERT INTO users (user_name, user_email, user_password, user_role, user_dob, user_bg, user_phone)
                VALUES (?, ?, ?, 'Doctor', '2000-01-01', 'N/A', 'N/A')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $userName, $userEmail, $userPassword);
        $result = $stmt->execute();
        $newUserId = $conn->insert_id;
        $stmt->close();
        return $result ? $newUserId : false;
    }

    // Create the doctor profile row linked to the user
    function createDoctorProfile($conn, $userId, $specializationId, $doctorBio, $doctorFee, $doctorPhoto, $doctorAvailability) {
        $sql = "INSERT INTO doctors (user_id, specialization_id, doctor_bio, doctor_fee, doctor_photo, doctor_availability)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissss", $userId, $specializationId, $doctorBio, $doctorFee, $doctorPhoto, $doctorAvailability);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Update doctor user info (name, email, password)
    function updateDoctorUser($conn, $userId, $userName, $userEmail, $userPassword) {
        $sql = "UPDATE users SET user_name = ?, user_email = ?, user_password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $userName, $userEmail, $userPassword, $userId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Update doctor profile info
    function updateDoctorProfile($conn, $doctorId, $specializationId, $doctorBio, $doctorFee, $doctorPhoto, $doctorAvailability) {
        $sql = "UPDATE doctors
                SET specialization_id = ?, doctor_bio = ?, doctor_fee = ?, doctor_photo = ?, doctor_availability = ?
                WHERE doctor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssi", $specializationId, $doctorBio, $doctorFee, $doctorPhoto, $doctorAvailability, $doctorId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Deactivate a doctor (set user_is_active = 0 instead of deleting)
    function deactivateDoctor($conn, $userId) {
        $sql = "UPDATE users SET user_is_active = 0 WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Get total doctor count and active doctor count for stats
    function getDoctorStats($conn) {
        $sql = "SELECT
                    COUNT(d.doctor_id) AS total_doctors,
                    SUM(CASE WHEN u.user_is_active = 1 THEN 1 ELSE 0 END) AS active_doctors
                FROM doctors d
                JOIN users u ON d.user_id = u.user_id";
        $result = $conn->query($sql);
        return $result->fetch_assoc();
    }

    // Get all active doctors for patient browsing (with optional specialization filter)
    function getActiveDoctors($conn, $specializationId = 0) {
        if ($specializationId > 0) {
            // Filter by specialization
            $sql = "SELECT d.doctor_id, d.doctor_fee, d.doctor_availability, d.doctor_photo, d.doctor_bio,
                           u.user_name,
                           s.specialization_name
                    FROM doctors d
                    JOIN users u ON d.user_id = u.user_id
                    JOIN specializations s ON d.specialization_id = s.specialization_id
                    WHERE u.user_is_active = 1 AND d.specialization_id = ?
                    ORDER BY u.user_name ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $specializationId);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        } else {
            // Get all active doctors
            $sql = "SELECT d.doctor_id, d.doctor_fee, d.doctor_availability, d.doctor_photo, d.doctor_bio,
                           u.user_name,
                           s.specialization_name
                    FROM doctors d
                    JOIN users u ON d.user_id = u.user_id
                    JOIN specializations s ON d.specialization_id = s.specialization_id
                    WHERE u.user_is_active = 1
                    ORDER BY u.user_name ASC";
            $result = $conn->query($sql);
            return $result;
        }
    }

    // Check if email already exists for another user (used when editing doctor)
    function emailExistsForOtherUser($conn, $userEmail, $userId) {
        $sql = "SELECT user_id FROM users WHERE user_email = ? AND user_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $userEmail, $userId);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Get top N active doctors ordered by highest appointment count (for homepage)
    function getTopDoctors($conn, $limit = 3) {
        $sql = "SELECT d.doctor_id, d.doctor_fee, d.doctor_photo, d.doctor_bio,
                       u.user_name,
                       s.specialization_name,
                       COUNT(a.appointment_id) AS total_appointments
                FROM doctors d
                JOIN users u ON d.user_id = u.user_id
                JOIN specializations s ON d.specialization_id = s.specialization_id
                LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
                WHERE u.user_is_active = 1
                GROUP BY d.doctor_id, u.user_id, s.specialization_id
                ORDER BY total_appointments DESC
                LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
}
?>
