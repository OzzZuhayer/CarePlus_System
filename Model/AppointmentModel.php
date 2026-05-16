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
}