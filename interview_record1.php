<?php
session_start();

// 檢查是否已登入
if (!isset($_SESSION['user_id'])) {
    header("Location: login1.php");
    exit();
}
include('header1.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test1";

// 建立連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $case_id = $_POST['case_id'];
    $interview_date = $_POST['interview_date'];
    $record = $_POST['record'];

    $sql = "INSERT INTO interview_records (case_id, interview_date, record) VALUES ('$case_id', '$interview_date', '$record')";

    if ($conn->query($sql) === TRUE) {
        $successMessage = "訪談紀錄提交成功！";
    } else {
        $errorMessage = "錯誤: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <title>新增訪談紀錄</title>
    <style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
        font-size: 18px;
    }

    .form-group input, 
    .form-group select, 
    .form-group textarea,
    .submit-btn {  /* 將所有輸入元素和按鈕統一寬度 */
        width: 100%;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box;  /* 確保 padding 不會影響整體寬度 */
    }

    /* 特別調整下拉選單 */
    #case_id {
        height: auto;
        appearance: none;
        background: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 12 12'%3E%3Cpath d='M6 9L1 4h10z'/%3E%3C/svg%3E") no-repeat right 15px center/12px 12px;
        padding-right: 40px;
    }

    /* 日期輸入框特別調整 */
    #interview_date {
        height: auto;
        appearance: none;
    }

    .form-group textarea {
        min-height: 200px;
        resize: vertical;  /* 只允許垂直調整大小 */
    }

    .submit-btn {
        background-color: #2c3e50;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 18px;
        font-weight: bold;
        margin-top: 10px;
    }

    .submit-btn:hover {
        background-color: #34495e;
    }

    .alert {
        padding: 20px;
        margin: 20px auto;
        border-radius: 8px;
        font-size: 18px;
        max-width: 800px;
        display: flex;
        align-items: center;
        position: relative;
        padding-left: 60px;
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .alert::before {
        content: '';
        position: absolute;
        left: 20px;
        width: 24px;
        height: 24px;
        background-size: contain;
        background-repeat: no-repeat;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .alert-success::before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23155724'%3E%3Cpath d='M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z'/%3E%3C/svg%3E");
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .alert-danger::before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23721c24'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z'/%3E%3C/svg%3E");
    }

    .content h1 {
        font-size: 32px;    /* 增加標題字體大小 */
        margin-bottom: 30px;
        color: #2c3e50;
        text-align: center;
        padding: 20px 0;
    }

    
</style>
</head>
<body>
    <div class="content">
        <h1>新增訪談紀錄</h1>
        
        <?php if(isset($successMessage)): ?>
            <div class="alert alert-success"><?= $successMessage ?></div>
        <?php endif; ?>
        <?php if(isset($errorMessage)): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="interview_record1.php" method="POST">
                <div class="form-group">
                    <label for="case_id" style="font-size: 16px;">選擇個案</label>
                    <select id="case_id" name="case_id" required style="font-size: 16px; padding: 10px;">
                        <option value="">選擇個案</option>
                        <?php
                        // 修改資料庫連接為正確的資料庫名稱
                        $conn = new mysqli('localhost', 'root', '', 'test1');
                        
                        // 使用 prepared statement 來防止 SQL 注入
                        $sql = "SELECT id, case_name FROM cases WHERE social_worker_id = ? AND status = 1";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $_SESSION['user_id']);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['case_name']) . "</option>";
                        }
                        $stmt->close();
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="interview_date" style="font-size: 16px;">訪談日期</label>
                    <input type="date" id="interview_date" name="interview_date" required style="font-size: 16px; padding: 10px;">
                </div>

                <div class="form-group">
                    <label for="record" style="font-size: 16px;">訪談紀錄</label>
                    <textarea id="record" name="record" rows="6" required style="font-size: 16px; padding: 10px;"></textarea>
                </div>

                <button type="submit" class="submit-btn" style="font-size: 16px; padding: 12px;">提交訪談紀錄</button>
            </form>
        </div>
    </div>
</body>
</html>