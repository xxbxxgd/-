<?php
session_start();
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
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 查詢用戶資料
    $sql = "SELECT * FROM social_workers WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // 比對密碼
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['is_admin'] = $row['is_admin'];
            header("Location: index1.php"); // 改為重定向到首頁
            exit();
        } else {
            $errorMessage = "密碼錯誤";
        }
    } else {
        $errorMessage = "用戶名不存在";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .login-body {
        min-height: calc(100vh - 60px);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        margin-top: -20px;
    }

    .card {
        background: rgba(255, 255, 255, 0.95);
        border: none;
        border-radius: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 500px;  /* 增加卡片寬度 */
        padding: 20px;     /* 增加內邊距 */
    }

    .card-header {
        border-bottom: none;
        text-align: center;
        padding: 30px 20px;  /* 增加上下內邊距 */
        background: transparent;
    }

    .card-header h2 {
        font-size: 32px;    /* 增加標題字體大小 */
        margin-bottom: 15px;
    }

    .card-header p {
        font-size: 18px;    /* 增加副標題字體大小 */
    }

    .form-label {
        font-size: 18px;    /* 增加標籤字體大小 */
        margin-bottom: 10px;
        font-weight: 500;
    }

    .form-control {
        border-radius: 0.8rem;
        padding: 15px;      /* 增加輸入框內邊距 */
        margin-bottom: 20px;
        font-size: 16px;    /* 增加輸入框字體大小 */
        height: auto;
    }

    .btn-primary {
        background: #6a11cb;
        border: none;
        padding: 15px;      /* 增加按鈕內邊距 */
        border-radius: 0.8rem;
        transition: background-color 0.3s;
        width: 100%;
        font-size: 18px;    /* 增加按鈕字體大小 */
        font-weight: bold;
    }

    .card-footer {
        padding: 20px;
        font-size: 16px;    /* 增加底部文字大小 */
    }

    .custom-alert {
        border-radius: 0.8rem;
        font-size: 16px;    /* 增加提示文字大小 */
        padding: 15px;
    }
</style>
</head>
<body>
<div class="login-body">
    <div class="card">
        <div class="card-header">
            <h2>歡迎回來</h2>
            <p class="text-muted">請使用您的帳號和密碼登入</p>
        </div>
        <div class="card-body">
            <?php if (isset($errorMessage)): ?>
                <div class="alert alert-danger custom-alert text-center">
                    <?= $errorMessage ?>
                </div>
            <?php endif; ?>
            <form action="login1.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">用戶名</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="輸入用戶名" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">密碼</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="輸入密碼" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">登入</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <small class="text-muted">還沒有帳號？<a href="register1.php" class="text-decoration-none" style="color: #6a11cb;"> 點擊註冊</a></small>
        </div>
    </div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>