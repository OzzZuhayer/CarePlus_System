<?php
session_start();
include_once "../Config/Db.php";
include_once "../Model/DoctorModel.php";

header('Content-Type: application/json');

if (!isset($_SESSION['loggedIn'])) {
    echo json_encode(['ok' => false, 'message' => 'Not logged in.']);
    exit();
}

$db = new Db();
$conn = $db->connection();
$doctorModel = new DoctorModel();

$specializationId = isset($_GET['specialization_id']) ? (int)$_GET['specialization_id'] : 0;
$result = $doctorModel->getActiveDoctors($conn, $specializationId);

$doctors = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = [
            'doctor_id'          => $row['doctor_id'],
            'user_name'          => $row['user_name'],
            'specialization_name'=> $row['specialization_name'],
            'doctor_fee'         => $row['doctor_fee'],
            'doctor_photo'       => $row['doctor_photo'],
        ];
    }
}

echo json_encode(['ok' => true, 'doctors' => $doctors]);
exit();
