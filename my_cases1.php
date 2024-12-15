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
                    $sql = "SELECT id, case_name, created_at, status 
                           FROM cases 
                           WHERE social_worker_id = ? 
                           ORDER BY created_at DESC";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $social_worker_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['id']}</td>";
                            echo "<td>{$row['case_name']}</td>";
                            echo "<td class='date-column'>{$row['created_at']}</td>";
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
                    $stmt->bind_param("i", $social_worker_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['case_name']}</td>";
                            echo "<td class='date-column'>{$row['interview_date']}</td>";
                            echo "<td class='record-details'>" . htmlspecialchars($row['record']) . "</td>";
                            echo "<td>
                                    <button class='delete-btn' onclick='confirmDeleteInterview({$row['id']})'>
                                        刪除
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
</body>
</html>
<?php $conn->close(); ?> 