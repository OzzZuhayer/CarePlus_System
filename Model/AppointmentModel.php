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
}
?>