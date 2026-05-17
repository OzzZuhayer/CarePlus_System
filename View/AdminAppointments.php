<?php
session_start();
include_once "../Config/SessionGuard.php";

if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Admin') {
    header("Location: Login.php");
    exit();
}

include_once "../Config/Db.php";
include_once "../Model/AppointmentModel.php";
include_once "../Model/DoctorModel.php";

$db = new Db();
$conn = $db->connection();
$appointmentModel = new AppointmentModel();
$doctorModel      = new DoctorModel();

// Read GET filters
$filterDoctorId = isset($_GET['doctor_id'])  ? (int)$_GET['doctor_id']        : 0;
$filterDate     = isset($_GET['filter_date']) ? trim($_GET['filter_date'])      : '';
$filterStatus   = isset($_GET['status'])      ? trim($_GET['status'])           : '';

// Get filtered appointments
$appointments = $appointmentModel->getFilteredAppointments($conn, $filterDoctorId, $filterDate, $filterStatus);

// Get all doctors for the filter dropdown
$allDoctors = $doctorModel->getAllDoctors($conn);

$activePage = 'appointments';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments — CarePlus Admin</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
<div class="layout">

    <?php include "AdminSidebar.php"; ?>

    <div class="main-content">

        <div class="page-header">
            <h1>Appointment Management</h1>
            <p>Filter, view and manage all appointments.</p>
        </div>

        <!-- Filters -->
        <div class="card" style="margin-bottom:16px;">
            <form method="GET" action="AdminAppointments.php">
                <div style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">

                    <div class="form-group" style="flex:1; min-width:160px;">
                        <label>Doctor</label>
                        <select name="doctor_id" class="form-control">
                            <option value="0">All Doctors</option>
                            <?php
                            if ($allDoctors && $allDoctors->num_rows > 0) {
                                while ($doc = $allDoctors->fetch_assoc()) {
                                    $sel = ($filterDoctorId == $doc['doctor_id']) ? 'selected' : '';
                                    echo "<option value='{$doc['doctor_id']}' $sel>{$doc['user_name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group" style="flex:1; min-width:160px;">
                        <label>Date</label>
                        <input type="date" name="filter_date" class="form-control"
                               value="<?= htmlspecialchars($filterDate) ?>">
                    </div>

                    <div class="form-group" style="flex:1; min-width:160px;">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            <?php
                            $statuses = ['Pending','Confirmed','Completed','Cancelled','No-Show'];
                            foreach ($statuses as $s) {
                                $sel = ($filterStatus === $s) ? 'selected' : '';
                                echo "<option value='$s' $sel>$s</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div style="display:flex; gap:8px;">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="AdminAppointments.php" class="btn btn-secondary">Reset</a>
                    </div>

                </div>
            </form>
        </div>
        <!-- Appointments Table -->
        <div class="card">              