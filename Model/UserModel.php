
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

    function getUserById($conn, $userId) {
        $sql = "SELECT user_id, user_name, user_email, user_role, user_dob, user_bg, user_phone, user_created_at
                FROM users
                WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
    
        // Update patient profile (name, dob, blood group, phone)
    function updateProfile($conn, $userId, $userName, $userDob, $userBg, $userPhone) {
        $sql = "UPDATE users
                SET user_name = ?, user_dob = ?, user_bg = ?, user_phone = ?
                WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $userName, $userDob, $userBg, $userPhone, $userId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    // Get just the password of a user (used for password change verification)
    function getUserPassword($conn, $userId) {
        $sql = "SELECT user_password FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? $row['user_password'] : null;
    }
     // Update the user's password
    function updatePassword($conn, $userId, $newPassword) {
        $sql = "UPDATE users SET user_password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newPassword, $userId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    // ADMIN - USER MANAGEMENT

    // Get all users for the admin user management page
    function getAllUsers($conn) {
        $sql = "SELECT user_id, user_name, user_email, user_role, user_is_active
                FROM users
                ORDER BY user_id ASC";
        $result = $conn->query($sql);
        return $result;
    }

    // Toggle a user's active status (1 = active, 0 = inactive)
    function toggleUserStatus($conn, $userId, $newStatus) {
        $sql = "UPDATE users SET user_is_active = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $newStatus, $userId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}   