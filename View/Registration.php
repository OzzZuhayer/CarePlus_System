<?php
session_start();
if(isset($_SESSION['loggedIn'])&& $_SESSION['loggeeIN']){
    if($_SESSION[user_role]=='Admin')header("Location:AdminDashboard.php");
    if($_SESSION[user_role]=='doctor')header("Location:DoctorDashboard.php");
    else header("Loction:PatientHome.php");
    exit();
  }  

  $errorMsg=isset($_GET['error']) ? urldecode($_GET['error']) : '';
  $successMsg=isset($_GET['success'])? urldecode($_GET['success']) : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration - Careplus Hospital </title>
    <link rel="stylesheet" herf="Registration.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    
</body>
</html>