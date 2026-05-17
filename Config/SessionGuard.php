<?php
// Define timeout per role in seconds
$timeoutMap = [
    'Patient' => 600,    // 10 minutes
    'Doctor'  => 18000,  // 5 hours
    'Admin'   => 18000,  // 5 hours
];

// Only run the timeout check if the user is logged in
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {

    $role    = $_SESSION['user_role'] ?? 'Patient';
    $timeout = $timeoutMap[$role] ?? 600;

    // Check when the user was last active
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];

        if ($inactive > $timeout) {
            // Session has expired — log them out
            session_unset();
            session_destroy();
            header("Location: ../View/Login.php?error=" . urlencode("Session expired. Please login again."));
            exit();
        }
    }

    // Update the last activity timestamp on every page load
    $_SESSION['last_activity'] = time();
}
