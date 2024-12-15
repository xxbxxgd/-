<?php
session_start();
if (!isset($_SESSION['user_id'])) {
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

$user_id = $_SESSION['user_id'];

$sql = "SELECT username, name, email FROM social_workers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if (!$user_data) {
    $errorMessage = "無法獲取用戶資料";
}

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST['username']);
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['password']);
    
    try {
        $conn->begin_transaction();
        
        if (!empty($new_password)) {
            // 如果要更新密碼
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE social_workers SET username=?, name=?, email=?, password=? WHERE id=?");
            $stmt->bind_param("ssssi", $new_username, $new_name, $new_email, $hashed_password, $user_id);
        } else {
            // 如果不更新密碼
            $stmt = $conn->prepare("UPDATE social_workers SET username=?, name=?, email=? WHERE id=?");
            $stmt->bind_param("sssi", $new_username, $new_name, $new_email, $user_id);
        }
        
        if ($stmt->execute()) {
            $conn->commit();
            $successMessage = "個人資料更新成功！";
            // 更新 session 中的資料
            $_SESSION['username'] = $new_username;
            $_SESSION['name'] = $new_name;
        } else {
            throw new Exception("更新失敗");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $errorMessage = "更新失敗：" . $e->getMessage();
    }
}

// 獲取當前用戶資料
$stmt = $conn->prepare("SELECT username, name, email FROM social_workers WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>個人資料設定</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
        }
        .profile-card {
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
            text-align: center;
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
            border: 1px solid #dee2e6;
            padding: 12px;
            transition: border-color 0.2s;
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
            width: 100%;
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
    </style>
</head>
<body class="bg-light">
    <div class="profile-container">
        <div class="profile-card">
            <h1 class="page-title">個人資料設定</h1>
            
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

            <form method="POST" class="needs-validation" novalidate>
                <div class="form-group">
                    <label class="form-label" for="username">
                        <i class="bi bi-person-fill me-2"></i>帳號
                    </label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?= htmlspecialchars($user_data['username']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="name">
                        <i class="bi bi-person-badge-fill me-2"></i>姓名
                    </label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= htmlspecialchars($user_data['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="bi bi-envelope-fill me-2"></i>電子郵件
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($user_data['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="bi bi-key-fill me-2"></i>新密碼
                    </label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="如不修改請留空">
                </div>

                <button type="submit" class="btn btn-update">
                    <i class="bi bi-check2-circle me-2"></i>更新資料
                </button>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 在我的個案列表的操作欄中添加編輯訪談按鈕 -->
    <td>
        <button class="btn btn-sm btn-primary" onclick="showEditInterviewModal(<?= $record['id'] ?>, '<?= $record['interview_date'] ?>', `<?= htmlspecialchars($record['interview_record'], ENT_QUOTES) ?>`)">
            <i class="bi bi-pencil-square me-1"></i>編輯
        </button>
    </td>

    <!-- 添加編輯訪談的 Modal -->
    <div class="modal fade" id="editInterviewModal" tabindex="-1" aria-labelledby="editInterviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInterviewModalLabel">
                        <i class="bi bi-journal-text me-2"></i>編輯訪談紀錄
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editInterviewForm">
                        <input type="hidden" id="edit_interview_id" name="interview_id">
                        <div class="mb-3">
                            <label for="edit_interview_date" class="form-label">
                                <i class="bi bi-calendar-date me-2"></i>訪談日期
                            </label>
                            <input type="date" class="form-control" id="edit_interview_date" name="interview_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_interview_record" class="form-label">
                                <i class="bi bi-journal-text me-2"></i>訪談紀錄
                            </label>
                            <textarea class="form-control" id="edit_interview_record" name="interview_record" rows="5" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="updateInterview()">更新紀錄</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 添加相關的 JavaScript -->
    <script>
    function showEditInterviewModal(id, date, record) {
        document.getElementById('edit_interview_id').value = id;
        document.getElementById('edit_interview_date').value = date;
        document.getElementById('edit_interview_record').value = record;
        new bootstrap.Modal(document.getElementById('editInterviewModal')).show();
    }

    function updateInterview() {
        const formData = new FormData(document.getElementById('editInterviewForm'));
        
        fetch('update_interview.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: '更新成功！',
                    text: data.message,
                    icon: 'success'
                }).then(() => {
                    location.reload();
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