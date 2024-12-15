<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['case_id'])) {
    $case_id = $_POST['case_id'];
    
    // 先獲取個案資訊，用於顯示在成功訊息中
    $case_info = $conn->prepare("SELECT case_name FROM cases WHERE id = ?");
    $case_info->bind_param("i", $case_id);
    $case_info->execute();
    $result = $case_info->get_result();
    $case_data = $result->fetch_assoc();
    $case_name = $case_data['case_name'];
    
    // 開始事務處理
    $conn->begin_transaction();
    
    try {
        // 先刪除相關的訪談記錄
        $sql1 = "DELETE FROM interview_records WHERE case_id = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("i", $case_id);
        $stmt1->execute();
        
        // 刪除個案基本資料
        $sql2 = "DELETE FROM `case information` WHERE case_name = (SELECT case_name FROM cases WHERE id = ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $case_id);
        $stmt2->execute();
        
        // 最後刪除個案
        $sql3 = "DELETE FROM cases WHERE id = ?";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("i", $case_id);
        $stmt3->execute();
        
        // 提交事務
        $conn->commit();
        
        // 回傳更詳細的成功訊息
        echo json_encode([
            'status' => 'success',
            'title' => '刪除成功！',
            'message' => "個案 \"$case_name\" 已成功刪除",
            'details' => [
                'case_id' => $case_id,
                'case_name' => $case_name,
                'deleted_at' => date('Y-m-d H:i:s')
            ]
        ]);
    } catch (Exception $e) {
        // 發生錯誤時回滾事務
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'title' => '刪除失敗',
            'message' => '無法刪除個案，請稍後再試',
            'error_details' => $e->getMessage()
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