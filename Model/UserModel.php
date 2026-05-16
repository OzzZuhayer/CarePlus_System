
<?php
class UserModel {

    // Check if an email already exists in the database
    function emailExists($conn, $userEmail) {
        $sql = "SELECT user_id FROM users WHERE user_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    function registerPatient($conn, $userName, $userEmail, $userPassword, $userDob, $userBg, $userPhone) {
        $sql = "INSERT INTO users (user_name, user_email, user_password, user_role, user_dob, user_bg, user_phone)
                VALUES (?, ?, ?, 'Patient', ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $userName, $userEmail, $userPassword, $userDob, $userBg, $userPhone);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    // Get a user by their email address (used during login)
    function getUserByEmail($conn, $userEmail) {
        $sql = "SELECT user_id, user_name, user_email, user_password, user_role, user_is_active
                FROM users
                WHERE user_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
}   