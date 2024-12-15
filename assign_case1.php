<?php
session_start();

// 檢查是否已登入且是管理員
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login1.php");
    exit();
}

// 將 header.php 的引入移到 DOCTYPE 之前
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
    $case_name = $_POST['case_name'];
    $social_worker_id = $_POST['social_worker_id'];

    $sql = "INSERT INTO cases (case_name, social_worker_id) VALUES ('$case_name', '$social_worker_id')";

    if ($conn->query($sql) === TRUE) {
        $successMessage = "個案分配成功！";
    } else {
        $errorMessage = "錯誤: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>分配新個案</title>
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
    .submit-btn {  /* 將按鈕加入相同的寬度設定 */
        width: 100%;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box;  /* 確保 padding 不會影響整體寬度 */
    }

    /* 特別調整下拉選單 */
    #social_worker_id {
        height: auto;
        appearance: none;
        background: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 12 12'%3E%3Cpath d='M6 9L1 4h10z'/%3E%3C/svg%3E") no-repeat right 15px center/12px 12px;
        padding-right: 40px;
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

    

    /* 增加輸入框焦點效果 */
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #2c3e50;
        box-shadow: 0 0 5px rgba(44, 62, 80, 0.2);
    }
</style>
</head>
<body>
    <div class="content">
        <h1>分配個案</h1>

        <?php if(isset($successMessage)): ?>
            <div class="alert alert-success"><?= $successMessage ?></div>
        <?php endif; ?>
        <?php if(isset($errorMessage)): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="assign_case1.php" method="POST">
                <div class="form-group">
                    <label for="case_name">個案名稱</label>
                    <select id="case_name" name="case_name" required>
                        <option value="">選擇個案</option>
                        <?php
                        // 獲取所有未分配的個案或社工已被刪除的個案
                        $conn = new mysqli('localhost', 'root', '', 'test1');
                        $sql = "SELECT c.id, c.case_name 
                                FROM cases c 
                                LEFT JOIN social_workers sw ON c.social_worker_id = sw.id 
                                WHERE c.social_worker_id IS NULL 
                                   OR sw.id IS NULL";
                        $result = $conn->query($sql);

                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['case_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="social_worker_id">社工選擇</label>
                    <select id="social_worker_id" name="social_worker_id" required>
                        <option value="">選擇社工</option>
                        <?php
                        $conn = new mysqli('localhost', 'root', '', 'test1');
                        $result = $conn->query("SELECT * FROM social_workers");

                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="submit-btn">確認送出</button>
            </form>
        </div>
    </div>
</body>
</html>