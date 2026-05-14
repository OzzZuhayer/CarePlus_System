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
     <div class="container">
        <div class="leftside">
            <div  class="overlay">
                <h1>Careplus Hospital</h1>
                <p>Appointment Booking System</p>
            <div class="feature-box">
                <h3>Why Choose US?</h3>
            <ul>
                <li>  ✔ Easy Appointment Booking </li>
                <li>  ✔ Professional Doctors </li>
                  <li>  ✔ Fast & Secure Service </li>
                    <li>  ✔ 24/7 Patient Support </li> 
            </ul>
            </div>
           </div>
        </div>
      <div class="right-side">  
        <div class="form-box">

        <div class="logo">
          <span>🏥</span>
        </div>

        <h2>Create Your Account</h2>
        <p class="sub-title">Register as a new patient</p>

        <!-- Show error message if any (from URL or JS) -->
        <?php if ($errorMsg): ?>
          <div class="alert alert-error">⚠ <?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <!-- Show success message if any -->
        <?php if ($successMsg): ?>
          <div class="alert alert-success">✓ <?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>

        <!-- Registration Form — action & method from 1st code -->
        <form action="../Controller/RegisterController.php" method="POST" id="registerForm">

          <div class="input-group">
            <label for="user_name">Full Name</label>
            <input type="text" id="user_name" name="user_name"
                   placeholder="Enter your full name" required>
          </div>

          <div class="input-group">
            <label for="user_email">Email Address</label>
            <input type="email" id="user_email" name="user_email"
                   placeholder="Enter your email" required>
          </div>

          <div class="input-group">
            <label for="user_password">Password</label>
            <div class="password-wrap">
              <input type="password" id="user_password" name="user_password"
                     placeholder="Enter your password" required>
              <button type="button" class="password-toggle"
                      onclick="togglePassword('user_password', this)">👁</button>
            </div>
          </div>

          <div class="input-group">
            <label for="user_dob">Date of Birth</label>
            <input type="date" id="user_dob" name="user_dob" required>
          </div>

          <div class="input-group">
            <label for="user_bg">Blood Group</label>
            <select id="user_bg" name="user_bg" required>
              <option value="">Select blood group</option>
              <option value="A+">A+</option>
              <option value="A-">A-</option>
              <option value="B+">B+</option>
              <option value="B-">B-</option>
              <option value="AB+">AB+</option>
              <option value="AB-">AB-</option>
              <option value="O+">O+</option>
              <option value="O-">O-</option>
            </select>
          </div>

          <div class="input-group">
            <label for="user_phone">Phone Number</label>
            <input type="text" id="user_phone" name="user_phone"
                   placeholder="Enter your phone number" required>
          </div>


</body>
</html>