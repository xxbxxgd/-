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
    $case_name = $_POST['case_name'];
    $client_name = $_POST['client_name'];
    $client_age = $_POST['client_age'];
    $client_contact = $_POST['client_contact'];
    
    try {
        $conn->begin_transaction();
        
        // 修改 cases 表的插入，將 social_worker_id 設為 NULL
        $stmt = $conn->prepare("INSERT INTO cases (case_name, status, social_worker_id) VALUES (?, 1, NULL)");
        $stmt->bind_param("s", $case_name);
        $stmt->execute();
        
        // 插入 case information 表
        $stmt = $conn->prepare("INSERT INTO `case information` (case_name, client_name, client_age, client_contact) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $case_name, $client_name, $client_age, $client_contact);
        $stmt->execute();
        
        $conn->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => '個案已成功新增'
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => '新增失敗：' . $e->getMessage()
        ]);
    }
}

$conn->close(); 