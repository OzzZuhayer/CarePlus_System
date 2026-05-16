
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
}   