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

// 獲取當前社工的ID
$social_worker_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的個案列表</title>
    <style>
        .cases-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .section {
            margin-bottom: 40px;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .section h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
            font-size: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        .record-details {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .date-column {
            white-space: nowrap;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }
        .page-title {
            text-align: center;
            color: #2c3e50;
            margin: 30px 0;
            font-size: 32px;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .edit-btn {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .edit-btn:hover {
            background-color: #0b5ed7;
        }
        .modal-content {
            border-radius: 15px;
        }
        .modal-header {
            border-radius: 15px 15px 0 0;
            padding: 1.5rem;
        }
        .form-control {
            border: 1px solid #e0e0e0;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #b69d74;
            box-shadow: 0 0 0 0.2rem rgba(182, 157, 116, 0.15);
        }
        .form-control:disabled {
            background-color: #f8f9fa;
        }
        .btn-primary {
            background: linear-gradient(45deg, #d4c19c, #b69d74);
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #c4b08b, #a68c63);
            transform: translateY(-1px);
        }
        .btn-light {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn-light:hover {
            background: #e9ecef;
        }
        .form-label {
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .bg-gradient-primary-to-secondary {
            background: linear-gradient(45deg, #d4c19c, #b69d74);
        }
        .modal-dialog {
            margin-top: 5vh;
        }
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <h1 class="page-title">我的個案管理</h1>
    
    <div class="cases-container">
        <!-- 我的個案列表 -->
        <div class="section">
            <h2>我的個案列表</h2>
            <table>
                <thead>
                    <tr>
                        <th>個案編號</th>
                        <th>個案名稱</th>
                        <th>分配日期</th>
                        <th>狀態</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT c.id, c.case_name, c.status, c.created_at as assignment_date 
                            FROM cases c 
                            WHERE c.social_worker_id = ? 
                            ORDER BY c.created_at ASC";
                    
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        die("準備查詢失敗: " . $conn->error);
                    }

                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['id']}</td>";
                            echo "<td>{$row['case_name']}</td>";
                            echo "<td class='date-column'>{$row['assignment_date']}</td>";
                            echo "<td class='status-" . ($row['status'] ? 'active' : 'inactive') . "'>" . 
                                 ($row['status'] ? '進行中' : '已結束') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>目前沒有被分配的個案</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- 訪談紀錄 -->
        <div class="section">
            <h2>訪談紀錄</h2>
            <table>
                <thead>
                    <tr>
                        <th>個案名稱</th>
                        <th>訪談日期</th>
                        <th>訪談內容</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT ir.id, c.case_name, ir.interview_date, ir.record 
                            FROM interview_records ir
                            JOIN cases c ON ir.case_id = c.id
                            WHERE c.social_worker_id = ?
                            ORDER BY ir.interview_date DESC";
                    
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        die("準備查詢失敗: " . $conn->error);
                    }

                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['case_name']}</td>";
                            echo "<td class='date-column'>{$row['interview_date']}</td>";
                            echo "<td class='record-details'>" . htmlspecialchars($row['record']) . "</td>";
                            echo "<td>
                                    <button class='edit-btn me-2' onclick='openEditWindow(" . $row['id'] . ")' style='background-color: #0d6efd;'>
                                        <i class='bi bi-pencil me-1'></i>編輯
                                    </button>
                                    <button class='delete-btn' onclick='confirmDeleteInterview(" . $row['id'] . ")'>
                                        <i class='bi bi-trash me-1'></i>刪除
                                    </button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>目前沒有訪談紀錄</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 添加 SweetAlert2 和刪除功能的 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function confirmDeleteInterview(interviewId) {
        Swal.fire({
            title: '確認刪除',
            text: '您確定要刪除這條訪談記錄嗎？此操作無法復原。',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '確認刪除',
            cancelButtonText: '取消'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('delete_interview1.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'interview_id=' + interviewId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: data.title,
                            text: data.message,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: data.title,
                            text: data.message,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }
    </script>

    <!-- 添加 JavaScript -->
    <script>
    function openEditWindow(id) {
        // 開啟新視窗
        window.open('edit_interview.php?id=' + id, 'EditInterview', 
            'width=800,height=600,resizable=yes,scrollbars=yes,status=yes');
    }
    </script>
</body>
</html>
<?php $conn->close(); ?> 