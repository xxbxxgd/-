<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => '未授權的訪問']);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => '資料庫連接失敗']);
    exit();
}

$interview_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM interview_records WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $interview_id);
$stmt->execute();
$result = $stmt->get_result();
$interview_data = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($interview_data);

$conn->close(); 