<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
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

$case_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT 
            c.id,
            c.case_name,
            c.status,
            ci.client_name,
            ci.client_age,
            ci.client_contact
        FROM cases c
        LEFT JOIN `case information` ci ON c.case_name = ci.case_name
        WHERE c.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $case_id);
$stmt->execute();
$result = $stmt->get_result();
$case_data = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($case_data);

$conn->close(); 