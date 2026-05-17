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
