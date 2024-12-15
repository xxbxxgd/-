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

$case_id = isset($_GET['id']) ? $_GET['id'] : 0;

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $case_name = $_POST['case_name'];
    $client_name = $_POST['client_name'];
    $client_age = $_POST['client_age'];
    $client_contact = $_POST['client_contact'];
    $status = $_POST['status'];
    
    try {
        $conn->begin_transaction();
        
        // 更新 cases 表
        $stmt = $conn->prepare("UPDATE cases SET case_name = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sii", $case_name, $status, $case_id);
        $stmt->execute();
        
        // 更新 case information 表
        $stmt = $conn->prepare("UPDATE `case information` SET client_name = ?, client_age = ?, client_contact = ? WHERE case_name = ?");
        $stmt->bind_param("siss", $client_name, $client_age, $client_contact, $case_name);
        $stmt->execute();
        
        $conn->commit();
        $successMessage = "個案資料更新成功！";
    } catch (Exception $e) {
        $conn->rollback();
        $errorMessage = "更新失敗：" . $e->getMessage();
    }
}

// 獲取個案資料
$sql = "SELECT 
            c.id,
            c.case_name,
            c.status,
            ci.client_name,
            ci.client_age,
            ci.client_contact
        FROM cases c
        LEFT JOIN `case information` ci ON c.case_name = ci.case_name
        WHERE c.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $case_id);
$stmt->execute();
$result = $stmt->get_result();
$case_data = $result->fetch_assoc();

if (!$case_data) {
    header("Location: admin_dashboard1.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯個案資料</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .edit-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
        }
        .edit-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .page-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4CAF50;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
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
        .btn-cancel {
            background-color: #6c757d;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-cancel:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-light">
    <div class="edit-container">
        <div class="edit-card">
            <h1 class="page-title">
                <i class="bi bi-pencil-square me-2"></i>編輯個案資料
            </h1>

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

            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="case_name">
                                <i class="bi bi-folder2 me-2"></i>個案名稱
                            </label>
                            <input type="text" class="form-control" id="case_name" name="case_name" 
                                   value="<?= htmlspecialchars($case_data['case_name']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="client_name">
                                <i class="bi bi-person me-2"></i>個案姓名
                            </label>
                            <input type="text" class="form-control" id="client_name" name="client_name" 
                                   value="<?= htmlspecialchars($case_data['client_name']) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="client_age">
                                <i class="bi bi-calendar3 me-2"></i>個案年齡
                            </label>
                            <input type="number" class="form-control" id="client_age" name="client_age" 
                                   value="<?= htmlspecialchars($case_data['client_age']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="client_contact">
                                <i class="bi bi-telephone me-2"></i>聯絡方式
                            </label>
                            <input type="text" class="form-control" id="client_contact" name="client_contact" 
                                   value="<?= htmlspecialchars($case_data['client_contact']) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-flag me-2"></i>個案狀態
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="status_active" 
                               value="1" <?= $case_data['status'] == 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status_active">進行中</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="status_inactive" 
                               value="0" <?= $case_data['status'] == 0 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status_inactive">已結束</label>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="admin_dashboard1.php" class="btn btn-cancel">
                        <i class="bi bi-arrow-left me-2"></i>返回
                    </a>
                    <button type="submit" class="btn btn-update">
                        <i class="bi bi-check2-circle me-2"></i>更新資料
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?> 