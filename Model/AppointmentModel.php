<?php

// Database operations related to appointments.

class AppointmentModel {

    // Get all booked time slots for a specific doctor on a specific date
    function getBookedSlots($conn, $doctorId, $appointmentDate) {
        $sql = "SELECT appointment_time FROM appointments
                WHERE doctor_id = ? AND appointment_date = ?
                AND appointment_status NOT IN ('Cancelled')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $doctorId, $appointmentDate);
        $stmt->execute();
        $result = $stmt->get_result();

        // booked times slots into a simple array
        $bookedSlots = [];
        while ($row = $result->fetch_assoc()) {
            $bookedSlots[] = $row['appointment_time'];
        }
        $stmt->close();
        return $bookedSlots;
    }

    // Check if a specific slot is still available to prevent double booking
    function isSlotAvailable($conn, $doctorId, $appointmentDate, $appointmentTime) {
        $sql = "SELECT appointment_id FROM appointments
                WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ?
                AND appointment_status NOT IN ('Cancelled')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $doctorId, $appointmentDate, $appointmentTime);
        $stmt->execute();
        $stmt->store_result();
        $available = $stmt->num_rows === 0;
        $stmt->close();
        return $available;
    }

    // Book a new appointment
    function bookAppointment($conn, $patientId, $doctorId, $appointmentDate, $appointmentTime, $appointmentMessage) {
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, appointment_message)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $patientId, $doctorId, $appointmentDate, $appointmentTime, $appointmentMessage);
        $result = $stmt->execute();
        $newId = $conn->insert_id;
        $stmt->close();
        return $result ? $newId : false;
    }

    // Get all appointments for a specific patient (for My Appointments page)
    function getPatientAppointments($conn, $patientId) {
        $sql = "SELECT a.appointment_id, a.appointment_date, a.appointment_time,
                       a.appointment_message, a.appointment_status, a.appointment_created_at,
                       u.user_name AS doctor_name,
                       s.specialization_name
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u ON d.user_id = u.user_id
                JOIN specializations s ON d.specialization_id = s.specialization_id
                WHERE a.patient_id = ?
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    // Cancel an appointment (patient can only cancel their own pending appointments)
    function cancelAppointment($conn, $appointmentId, $patientId) {
        $sql = "UPDATE appointments SET appointment_status = 'Cancelled'
                WHERE appointment_id = ? AND patient_id = ? AND appointment_status = 'Pending'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $appointmentId, $patientId);
        $result = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected > 0;
    }

    // Count upcoming appointments for a patient (used on patient dashboard)
    function countUpcomingAppointments($conn, $patientId) {
        $today = date('Y-m-d');
        $sql = "SELECT COUNT(*) AS total FROM appointments
                WHERE patient_id = ? AND appointment_date >= ?
                AND appointment_status IN ('Pending', 'Confirmed')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $patientId, $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }

    // Count completed appointments for a patient
    function countCompletedAppointments($conn, $patientId) {
        $sql = "SELECT COUNT(*) AS total FROM appointments
                WHERE patient_id = ? AND appointment_status = 'Completed'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }

    // Count cancelled appointments for a patient
    function countCancelledAppointments($conn, $patientId) {
        $sql = "SELECT COUNT(*) AS total FROM appointments
                WHERE patient_id = ? AND appointment_status = 'Cancelled'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }

    // Get today's appointments for a specific doctor
    function getTodaysAppointments($conn, $doctorId) {
        $today = date('Y-m-d');
        $sql = "SELECT a.appointment_id, a.appointment_time, a.appointment_message, a.appointment_status,
                       u.user_name AS patient_name
                FROM appointments a
                JOIN users u ON a.patient_id = u.user_id
                WHERE a.doctor_id = ? AND a.appointment_date = ?
                ORDER BY a.appointment_time ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $doctorId, $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    // Get today's confirmed appointments for a doctor (only Confirmed status)
    function getTodaysConfirmedAppointments($conn, $doctorId) {
        $today = date('Y-m-d');
        $sql = "SELECT a.appointment_id, a.appointment_time, a.appointment_message, a.appointment_status,
                       u.user_name AS patient_name
                FROM appointments a
                JOIN users u ON a.patient_id = u.user_id
                WHERE a.doctor_id = ? AND a.appointment_date = ? AND a.appointment_status = 'Confirmed'
                ORDER BY a.appointment_time ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $doctorId, $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    // Get appointments for a doctor for the current week (Mon-Fri)
    function getWeeklyAppointments($conn, $doctorId, $weekStart, $weekEnd) {
        $sql = "SELECT a.appointment_id, a.appointment_date, a.appointment_time,
                       a.appointment_message, a.appointment_status,
                       u.user_name AS patient_name
                FROM appointments a
                JOIN users u ON a.patient_id = u.user_id
                WHERE a.doctor_id = ? AND a.appointment_date BETWEEN ? AND ?
                ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $doctorId, $weekStart, $weekEnd);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    // Get all appointments for admin view
    function getAllAppointments($conn) {
        $sql = "SELECT a.appointment_id, a.appointment_date, a.appointment_time,
                       a.appointment_message, a.appointment_status,
                       u_patient.user_name AS patient_name,
                       u_doctor.user_name AS doctor_name,
                       s.specialization_name
                FROM appointments a
                JOIN users u_patient ON a.patient_id = u_patient.user_id
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u_doctor ON d.user_id = u_doctor.user_id
                JOIN specializations s ON d.specialization_id = s.specialization_id
                ORDER BY a.appointment_date DESC, a.appointment_time ASC";
        $result = $conn->query($sql);
        return $result;
    }

    // Update appointment status (used by doctor and admin)
    function updateAppointmentStatus($conn, $appointmentId, $newStatus) {
        $sql = "UPDATE appointments SET appointment_status = ? WHERE appointment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newStatus, $appointmentId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Get total appointment count (for admin stats)
    function getTotalAppointmentCount($conn) {
        $sql = "SELECT COUNT(*) AS total FROM appointments";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Get today's appointment count (for admin stats)
    function getTodaysAppointmentCount($conn) {
        $today = date('Y-m-d');
        $sql = "SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = '$today'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Get appointments with optional filters: doctor, date, status (for admin appointments page)
    function getFilteredAppointments($conn, $doctorId = 0, $filterDate = '', $filterStatus = '') {
        $where = [];
        $params = [];
        $types = '';

        if ($doctorId > 0) {
            $where[] = "a.doctor_id = ?";
            $params[] = $doctorId;
            $types .= 'i';
        }
        if (!empty($filterDate)) {
            $where[] = "a.appointment_date = ?";
            $params[] = $filterDate;
            $types .= 's';
        }
        if (!empty($filterStatus)) {
            $where[] = "a.appointment_status = ?";
            $params[] = $filterStatus;
            $types .= 's';
        }

        $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT a.appointment_id, a.appointment_date, a.appointment_time,
                       a.appointment_message, a.appointment_status,
                       u_patient.user_name AS patient_name,
                       u_doctor.user_name AS doctor_name,
                       s.specialization_name
                FROM appointments a
                JOIN users u_patient ON a.patient_id = u_patient.user_id
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u_doctor ON d.user_id = u_doctor.user_id
                JOIN specializations s ON d.specialization_id = s.specialization_id
                $whereClause
                ORDER BY a.appointment_date DESC, a.appointment_time ASC";

        if (count($params) > 0) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        return $conn->query($sql);
    }

    // Get today and tomorrow appointments with Pending or Confirmed status (for admin dashboard table)
    function getDashboardAppointments($conn) {
        $today    = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $sql = "SELECT a.appointment_id, a.appointment_date, a.appointment_time,
                       a.appointment_message, a.appointment_status,
                       u_patient.user_name AS patient_name,
                       u_doctor.user_name  AS doctor_name,
                       s.specialization_name
                FROM appointments a
                JOIN users u_patient ON a.patient_id = u_patient.user_id
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u_doctor ON d.user_id = u_doctor.user_id
                JOIN specializations s ON d.specialization_id = s.specialization_id
                WHERE a.appointment_date IN (?, ?)
                  AND a.appointment_status = 'Pending'
                ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $today, $tomorrow);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    // Cancel appointment with a reason (admin use)
    function cancelWithReason($conn, $appointmentId, $reason) {
        $sql = "UPDATE appointments SET appointment_status = 'Cancelled', appointment_message = CONCAT(appointment_message, ' [Cancelled: ', ?, ']') WHERE appointment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $reason, $appointmentId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Get total completed appointment count (for homepage tagline)
    function getCompletedAppointmentCount($conn) {
        $sql = "SELECT COUNT(*) AS total FROM appointments WHERE appointment_status = 'Completed'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
