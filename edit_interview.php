<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login1.php");
    exit();
}

// 資料庫連線設定
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? $_GET['id'] : 0;

// 獲取訪談記錄數據
$sql = "SELECT ir.*, c.case_name 
        FROM interview_records ir
        JOIN cases c ON ir.case_id = c.id
        WHERE ir.id = ? AND c.social_worker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "找不到記錄或無權限";
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯訪談紀錄</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background: linear-gradient(45deg, #d4c19c, #b69d74);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1rem 1.5rem;
        }
        .form-control:focus {
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
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-pencil-square me-2"></i>編輯訪談紀錄
            </h5>
        </div>
        <div class="card-body p-4">
            <form id="editForm">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-folder2 me-2"></i>個案名稱
                    </label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['case_name']) ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-calendar-date me-2"></i>訪談日期
                    </label>
                    <input type="date" class="form-control" name="interview_date" 
                           value="<?= $data['interview_date'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-journal-text me-2"></i>訪談紀錄
                    </label>
                    <textarea class="form-control" name="record" rows="4" required><?= htmlspecialchars($data['record']) ?></textarea>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-secondary me-2" onclick="window.close()">
                        <i class="bi bi-x-circle me-2"></i>取消
                    </button>
                    <button type="button" class="btn btn-primary" onclick="updateRecord()">
                        <i class="bi bi-check-circle me-2"></i>更新
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function updateRecord() {
        const formData = new FormData(document.getElementById('editForm'));
        
        fetch('update_interview.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: '更新成功！',
                    text: '訪談紀錄已更新',
                    icon: 'success'
                }).then(() => {
                    window.opener.location.reload(); // 重新整理父視窗
                    window.close(); // 關閉當前視窗
                });
            } else {
                Swal.fire({
                    title: '更新失敗',
                    text: data.message,
                    icon: 'error'
                });
            }
        });
    }
    </script>
</body>
</html> 