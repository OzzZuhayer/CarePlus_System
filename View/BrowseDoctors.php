<?php
session_start();
include_once "../Config/SessionGuard.php";

if (!isset($_SESSION['loggedIn']) || $_SESSION['user_role'] != 'Patient') {
    header("Location: Login.php");
    exit();
}

include_once "../Config/Db.php";
include_once "../Model/DoctorModel.php";

$db = new Db();
$conn = $db->connection();
$doctorModel = new DoctorModel();

$specializations = $doctorModel->getAllSpecializations($conn);
$doctors = $doctorModel->getActiveDoctors($conn, 0);

$activePage = 'doctors';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Doctors — CarePlus</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
<div class="layout">

    <?php include "PatientSidebar.php"; ?>

    <div class="main-content">

        <div class="page-header">
            <h1>Find Your Doctor</h1>
            <p>Browse our specialists and book an appointment.</p>
        </div>

        <div class="filter-bar">
            <input type="text" id="doctorSearch" class="form-control"
                   placeholder="Search by doctor name..."
                   onkeyup="searchTable('doctorSearch', 'doctorCardContainer')"
                   style="max-width:300px;">

            <select id="specFilter" class="form-control" style="max-width:220px;"
                    onchange="filterBySpecialization(this.value)">
                <option value="0">All Specializations</option>
                <?php while ($spec = $specializations->fetch_assoc()): ?>
                    <option value="<?= $spec['specialization_id'] ?>"><?= htmlspecialchars($spec['specialization_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="doctor-grid" id="doctorCardContainer">
            <?php if ($doctors && $doctors->num_rows > 0): ?>
                <?php while ($row = $doctors->fetch_assoc()):
                    $photoSrc = !empty($row['doctor_photo'])
                        ? '../' . $row['doctor_photo']
                        : '../Asset/Public/Uploads/Doctors/default.png';
                ?>
                <div class="doctor-card" data-name="<?= strtolower($row['user_name']) ?>">
                    <img src="<?= htmlspecialchars($photoSrc) ?>"
                         class="doctor-card-photo" alt="<?= htmlspecialchars($row['user_name']) ?>">
                    <div class="doctor-card-body">
                        <div class="doctor-card-name"><?= htmlspecialchars($row['user_name']) ?></div>
                        <div class="doctor-card-spec"><?= htmlspecialchars($row['specialization_name']) ?></div>
                        <div class="doctor-card-fee" style="margin-top:8px;">
                            Fee: <strong><?= htmlspecialchars($row['doctor_fee']) ?> TK</strong>
                        </div>
                        <div style="margin-top:14px;">
                            <a href="BookAppointment.php?doctor_id=<?= $row['doctor_id'] ?>"
                               class="btn btn-primary" style="width:100%; justify-content:center;">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="color:#6b7280; padding:20px;">No doctors found.</div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script src="../Script/DoctorFilter.js"></script>
<script src="../Script/Validation.js"></script>
</body>
</html>
