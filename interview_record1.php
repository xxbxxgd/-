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

$edit_mode = false;
$interview_data = null;

if (isset($_GET['id'])) {
    $edit_mode = true;
    $interview_id = $_GET['id'];
    
    // 獲取訪談記錄數據
    $sql = "SELECT ir.*, c.case_name 
            FROM interview_records ir
            JOIN cases c ON ir.case_id = c.id
            WHERE ir.id = ? AND c.social_worker_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $interview_id, $_SESSION['user_id']);
    $stmt->execute();
    $interview_data = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $case_id = $_POST['case_id'];
    $interview_date = $_POST['interview_date'];
    $record = $_POST['record'];

    if ($edit_mode) {
        $sql = "UPDATE interview_records SET case_id = ?, interview_date = ?, record = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $case_id, $interview_date, $record, $interview_id);
    } else {
        $sql = "INSERT INTO interview_records (case_id, interview_date, record) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $case_id, $interview_date, $record);
    }

    $success = $stmt->execute();
    
    if ($success) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'title' => '成功！',
            'message' => $edit_mode ? '訪談紀錄更新成功！' : '訪談紀錄提交成功！'
        ];
        header("Location: " . $_SERVER['PHP_SELF'] . ($edit_mode ? "?id=$interview_id" : ""));
        exit();
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => '錯誤',
            'message' => '錯誤: ' . $stmt->error
        ];
        header("Location: " . $_SERVER['PHP_SELF'] . ($edit_mode ? "?id=$interview_id" : ""));
        exit();
    }
}

// 在 HTML 之前加入這段處理提示的代碼
if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '{$alert['type']}',
                title: '{$alert['title']}',
                text: '{$alert['message']}',
                confirmButtonText: '確定'
            });
        });
    </script>";
    unset($_SESSION['alert']);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增訪談紀錄</title>
    <!-- 確保 SweetAlert2 在最前面載入 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- 其他 CSS 和 JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .bg-gradient-primary-to-secondary {
            background: linear-gradient(45deg, #d4c19c, #b69d74);
        }

        .form-control, .form-select {
            border: 1px solid #e0e0e0;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #b69d74;
            box-shadow: 0 0 0 0.2rem rgba(182, 157, 116, 0.15);
        }

        .btn-primary {
            background: linear-gradient(45deg, #d4c19c, #b69d74);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #c4b08b, #a68c63);
        }

        .form-label {
            color: #6c5a3d;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container py-3">
        <div class="card shadow border-0 rounded-3">
            <div class="card-header bg-gradient-primary-to-secondary text-white py-3">
                <h5 class="card-title mb-0">
                    <i class="bi bi-journal-plus me-2"></i>
                    <?= $edit_mode ? '編輯訪談紀錄' : '新增訪談紀錄' ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="interview_id" value="<?= $interview_data['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="case_id" class="form-label">
                                    <i class="bi bi-folder2 me-2"></i>個案名稱
                                </label>
                                <?php if ($edit_mode): ?>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($interview_data['case_name']) ?>" disabled>
                                    <input type="hidden" name="case_id" value="<?= $interview_data['case_id'] ?>">
                                <?php else: ?>
                                    <select class="form-select" id="case_id" name="case_id" required>
                                        <option value="" selected disabled>請選擇個案...</option>
                                        <?php
                                        $conn = new mysqli('localhost', 'root', '', 'test1');
                                        $sql = "SELECT c.id, c.case_name 
                                                FROM cases c 
                                                WHERE c.social_worker_id = ? 
                                                AND c.status = 1";
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
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="interview_date" class="form-label">
                                    <i class="bi bi-calendar-date me-2"></i>訪談日期
                                </label>
                                <input type="date" class="form-control" id="interview_date" name="interview_date" 
                                       value="<?= $edit_mode ? $interview_data['interview_date'] : '' ?>" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="record" class="form-label">
                                    <i class="bi bi-journal-text me-2"></i>訪談紀錄
                                </label>
                                <textarea class="form-control" id="record" name="record" rows="4" required><?= $edit_mode ? htmlspecialchars($interview_data['record']) : '' ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i><?= $edit_mode ? '更新紀錄' : '提交紀錄' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>