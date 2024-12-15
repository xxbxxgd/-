<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login1.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['interview_id'])) {
    $interview_id = $_POST['interview_id'];
    $user_id = $_SESSION['user_id'];
    
    // 檢查是否為管理員或該訪談記錄的擁有者
    if (!$_SESSION['is_admin']) {
        $check_stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM interview_records ir
            JOIN cases c ON ir.case_id = c.id
            WHERE ir.id = ? AND c.social_worker_id = ?
        ");
        $check_stmt->bind_param("ii", $interview_id, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $check_data = $check_result->fetch_assoc();
        
        if ($check_data['count'] == 0) {
            echo json_encode([
                'status' => 'error',
                'title' => '權限不足',
                'message' => '您沒有權限刪除此訪談記錄'
            ]);
            exit();
        }
    }
    
    // 獲取訪談記錄資訊
    $stmt = $conn->prepare("SELECT ir.id, c.case_name, ir.interview_date 
                           FROM interview_records ir 
                           JOIN cases c ON ir.case_id = c.id 
                           WHERE ir.id = ?");
    $stmt->bind_param("i", $interview_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $interview_data = $result->fetch_assoc();
    
    // 刪除訪談記錄
    $delete_stmt = $conn->prepare("DELETE FROM interview_records WHERE id = ?");
    $delete_stmt->bind_param("i", $interview_id);
    
    if ($delete_stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'title' => '刪除成功！',
            'message' => "已刪除 {$interview_data['case_name']} 於 {$interview_data['interview_date']} 的訪談記錄",
            'details' => [
                'interview_id' => $interview_id,
                'case_name' => $interview_data['case_name'],
                'interview_date' => $interview_data['interview_date'],
                'deleted_at' => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'title' => '刪除失敗',
            'message' => '無法刪除訪談記錄，請稍後再試'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'title' => '無效請求',
        'message' => '請求無效或缺少必要參數'
    ]);
}

$conn->close();
?> 