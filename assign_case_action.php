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
    $social_worker_id = $_POST['social_worker_id'];
    
    try {
        // 更新個案的社工
        $stmt = $conn->prepare("UPDATE cases SET social_worker_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $social_worker_id, $case_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => '個案已成功分配給社工'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => '分配失敗：找不到指定的個案'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => '分配失敗：' . $e->getMessage()
        ]);
    }
}

$conn->close(); 