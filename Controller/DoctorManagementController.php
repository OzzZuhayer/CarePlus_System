<?php

session_start();
include_once "../Config/SessionGuard.php";

include_once "../Config/Db.php";
include_once "../Model/DoctorModel.php";
include_once "../Model/UserModel.php";

// Only admins can manage doctors
if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Admin') {
    header("Location: ../View/Login.php");
    exit();
}