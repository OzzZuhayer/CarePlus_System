<?php

session_start();

// Destroy all session data (log the user out)
session_unset();
session_destroy();

// Send them back to the login page
header("Location: ../View/Login.php");
exit();
