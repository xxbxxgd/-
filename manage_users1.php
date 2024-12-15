<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login1.php");
    exit();
}

include('header1.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_username = trim($_POST['username']);
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['password']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    try {
        $conn->begin_transaction();
        
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE social_workers SET username=?, name=?, email=?, password=?, is_admin=? WHERE id=?");
            $stmt->bind_param("ssssis", $new_username, $new_name, $new_email, $hashed_password, $is_admin, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE social_workers SET username=?, name=?, email=?, is_admin=? WHERE id=?");
            $stmt->bind_param("sssis", $new_username, $new_name, $new_email, $is_admin, $user_id);
        }
        
        if ($stmt->execute()) {
            $conn->commit();
            $successMessage = "用戶資料更新成功！";
        } else {
            throw new Exception("更新失敗");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $errorMessage = "更新失敗：" . $e->getMessage();
    }
}

// 獲取所有用戶資料
$result = $conn->query("SELECT * FROM social_workers ORDER BY id");
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用戶管理</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .users-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .user-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            padding: 25px;
            transition: transform 0.2s;
        }
        .user-card:hover {
            transform: translateY(-5px);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 12px;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        .admin-radio-group {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }
        .btn-update {
            background-color: #4CAF50;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-update:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }
        .alert {
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 25px;
        }
        .page-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4CAF50;
        }
    </style>
</head>
<body class="bg-light">
    <div class="users-container">
        <h1 class="page-title">用戶管理</h1>
        
        <?php if(isset($successMessage)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= $successMessage ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($errorMessage)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= $errorMessage ?>
            </div>
        <?php endif; ?>

        <?php while($user = $result->fetch_assoc()): ?>
            <div class="user-card">
                <form method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="username_<?= $user['id'] ?>">
                                    <i class="bi bi-person-fill me-2"></i>帳號
                                </label>
                                <input type="text" class="form-control" id="username_<?= $user['id'] ?>" 
                                       name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name_<?= $user['id'] ?>">
                                    <i class="bi bi-person-badge-fill me-2"></i>姓名
                                </label>
                                <input type="text" class="form-control" id="name_<?= $user['id'] ?>" 
                                       name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="email_<?= $user['id'] ?>">
                                    <i class="bi bi-envelope-fill me-2"></i>電子郵件
                                </label>
                                <input type="email" class="form-control" id="email_<?= $user['id'] ?>" 
                                       name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="password_<?= $user['id'] ?>">
                                    <i class="bi bi-key-fill me-2"></i>新密碼
                                </label>
                                <input type="password" class="form-control" id="password_<?= $user['id'] ?>" 
                                       name="password" placeholder="如不修改請留空">
                            </div>
                        </div>
                    </div>

                    <div class="admin-radio-group mb-4">
                        <label class="form-label mb-3">
                            <i class="bi bi-shield-fill me-2"></i>管理員權限
                        </label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_admin" 
                                   id="admin_yes_<?= $user['id'] ?>" value="1" 
                                   <?= $user['is_admin'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="admin_yes_<?= $user['id'] ?>">是</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_admin" 
                                   id="admin_no_<?= $user['id'] ?>" value="0" 
                                   <?= !$user['is_admin'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="admin_no_<?= $user['id'] ?>">否</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-update">
                        <i class="bi bi-check2-circle me-2"></i>更新用戶資料
                    </button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>