<?php
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
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    // 檢查所有欄位是否已填寫
    if (empty($username) || empty($password) || empty($name) || empty($email)) {
        $errorMessage = "<div class='alert alert-danger' role='alert'>所有欄位均為必填項。</div>";
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT); // 密碼加密

        $sql = "INSERT INTO social_workers (username, password, name, email) VALUES ('$username', '$password', '$name', '$email')";

        if ($conn->query($sql) === TRUE) {
            $successMessage = "<div class='alert alert-success' role='alert'>社工帳號註冊成功！</div>";
        } else {
            $errorMessage = "<div class='alert alert-danger' role='alert'>錯誤: " . $conn->error . "</div>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <!-- 其他標籤 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>社工資料管理系統 - 註冊</title>
    <style>
    /* 基本樣式 */
    .form-container {
        max-width: 800px;  /* 增加寬度 */
        margin: 0 auto;
        padding: 40px;     /* 增加內邊距 */
    }

    .form-group {
        margin-bottom: 25px;  /* 增加間距 */
    }

    .form-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
        font-size: 18px;    /* 增加字體大小 */
    }

    .form-group input, 
    .form-group select, 
    .form-group textarea,
    .submit-btn {  /* 將 submit-btn 加入相同的寬度設定 */
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box;  /* 確保 padding 不會影響整體寬度 */
    }

    .submit-btn {
        background-color: #2c3e50;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 18px;
        margin-top: 10px;  /* 可選：增加與上方表單的間距 */
    }

    .submit-btn:hover {
        background-color: #34495e;
    }

    .alert {
        padding: 15px;      /* 增加提示框內邊距 */
        margin-bottom: 20px;
        border-radius: 4px;
        font-size: 16px;    /* 增加提示文字大小 */
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
        <h1>社工帳號註冊</h1>
        
        <?php if(isset($successMessage)): ?>
            <div class="alert alert-success"><?= $successMessage ?></div>
        <?php endif; ?>
        <?php if(isset($errorMessage)): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="register1.php" method="POST">
                <div class="form-group">
                    <label for="username">帳號</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">密碼</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="name">姓名</label>
                    <input type="text" id="name" name="name">
                </div>

                <div class="form-group">
                    <label for="email">電子郵件</label>
                    <input type="email" id="email" name="email">
                </div>

                <button type="submit" class="submit-btn">註冊</button>
            </form>
        </div>
    </div>
</body>
</html>
