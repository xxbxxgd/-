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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $case_id = $_POST['case_id'];
    $case_name = $_POST['case_name'];
    $client_name = $_POST['client_name'];
    $client_age = $_POST['client_age'];
    $client_contact = $_POST['client_contact'];
    $status = $_POST['status'];
    
    try {
        $conn->begin_transaction();
        
        // 更新 cases 表
        $stmt = $conn->prepare("UPDATE cases SET case_name = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sii", $case_name, $status, $case_id);
        $stmt->execute();
        
        // 更新 case information 表
        $stmt = $conn->prepare("UPDATE `case information` SET client_name = ?, client_age = ?, client_contact = ? WHERE case_name = ?");
        $stmt->bind_param("siss", $client_name, $client_age, $client_contact, $case_name);
        $stmt->execute();
        
        $conn->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => '個案資料已成功更新'
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => '更新失敗：' . $e->getMessage()
        ]);
    }
}

$conn->close(); 