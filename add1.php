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
    // 新增個案與基本資料
    $case_name = $_POST['case_name'];
    $client_name = $_POST['client_name'];
    $client_age = $_POST['client_age'];
    $client_contact = $_POST['client_contact'];

    // 修改 SQL 語句，將這些資料存入 case information 表
    $sql = "INSERT INTO `case information` (case_name, client_name, client_age, client_contact) 
            VALUES (?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $case_name, $client_name, $client_age, $client_contact);
    
    if ($stmt->execute()) {
        // 同時在 cases 表中創建一條記錄
        $sql2 = "INSERT INTO cases (case_name) VALUES (?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("s", $case_name);
        
        if ($stmt2->execute()) {
            $successMessage = "個案與基本資料已成功新增！";
        } else {
            $errorMessage = "錯誤: " . $conn->error;
        }
        $stmt2->close();
    } else {
        $errorMessage = "錯誤: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <title>新增個案</title>
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

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
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
            width: 100%;
            padding: 15px;
            border-radius: 4px;
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
            font-size: 32px;
            margin-bottom: 30px;
            color: #2c3e50;
            text-align: center;
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>新增個案與基本資料</h1>

        <?php if(isset($successMessage)): ?>
            <div class="alert alert-success"><?= $successMessage ?></div>
        <?php endif; ?>
        <?php if(isset($errorMessage)): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="add1.php" method="POST">
                <div class="form-group">
                    <label for="case_name">個案名稱</label>
                    <input type="text" id="case_name" name="case_name" required>
                </div>

                <div class="form-group">
                    <label for="client_name">個案姓名</label>
                    <input type="text" id="client_name" name="client_name" required>
                </div>

                <div class="form-group">
                    <label for="client_age">個案年齡</label>
                    <input type="number" id="client_age" name="client_age" min="0" required>
                </div>

                <div class="form-group">
                    <label for="client_contact">聯絡方式</label>
                    <input type="text" id="client_contact" name="client_contact" required>
                </div>

                <button type="submit" class="submit-btn">新增個案</button>
            </form>
        </div>
    </div>
</body>
</html>