<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login1.php");
    exit();
}
include('header1.php');$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test1";

// 建立連接


$conn = new mysqli('localhost', 'root', '', 'test1');
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>所有紀錄 - 管理員視圖</title>
    <style>
        .records-container {
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
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-buttons button {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }

        .confirm-delete {
            background-color: #dc3545;
            color: white;
        }

        .cancel-delete {
            background-color: #6c757d;
            color: white;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="records-container">
        <!-- 個案分配紀錄 -->
        <div class="section">
            <h2>個案分配紀錄</h2>
            <table>
                <thead>
                    <tr>
                        <th>個案編號</th>
                        <th>個案名稱</th>
                        <th>負責社工</th>
                        <th>分配日期</th>
                        <th>狀態</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("
                        SELECT c.id, c.case_name, sw.name as social_worker_name, 
                               c.created_at, c.status
                        FROM cases c 
                        LEFT JOIN social_workers sw ON c.social_worker_id = sw.id
                        ORDER BY c.id ASC
                    ");
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['id']}</td>";
                            echo "<td>{$row['case_name']}</td>";
                            echo "<td>" . ($row['social_worker_name'] ? $row['social_worker_name'] : '尚未分配社工') . "</td>";
                            echo "<td class='date-column'>{$row['created_at']}</td>";
                            echo "<td>";
                            echo "<span class='status-badge " . ($row['status'] ? 'status-active' : 'status-inactive') . "'>" . 
                                 ($row['status'] ? '進行中' : '已結束') . "</span>";
                            echo "</td>";
                            echo "<td>";
                            echo "<button class='delete-btn' onclick='confirmDelete({$row['id']})'>刪除</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>查詢出錯: " . $conn->error . "</td></tr>";
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
                        <th>訪談編號</th>
                        <th>個案名稱</th>
                        <th>負責社工</th>
                        <th>訪談日期</th>
                        <th>訪談內容</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("
                    SELECT ir.id, c.case_name, sw.name as social_worker_name,
                           ir.interview_date, ir.record
                    FROM interview_records ir  /* 修��這裡：從 interview 改為 interview_records */
                    LEFT JOIN cases c ON ir.case_id = c.id
                    LEFT JOIN social_workers sw ON c.social_worker_id = sw.id
                    ORDER BY ir.interview_date DESC
                ");
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['id']}</td>";
                            echo "<td>{$row['case_name']}</td>";
                            echo "<td>{$row['social_worker_name']}</td>";
                            echo "<td class='date-column'>{$row['interview_date']}</td>";
                            echo "<td class='record-details'>" . htmlspecialchars($row['record']) . "</td>";
                            echo "<td>";
                            echo "<button class='delete-btn' onclick='confirmDeleteInterview({$row['id']})'>刪除</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>查詢出錯: " . $conn->error . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- 社��活動統計 -->
        <div class="section">
            <h2>社工活動統計</h2>
            <table>
                <thead>
                    <tr>
                        <th>社工姓名</th>
                        <th>負責個案數</th>
                        <th>本月訪談次數</th>
                        <th>總訪談次數</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("
                    SELECT 
                        sw.name,
                        COUNT(DISTINCT c.id) as case_count,
                        COUNT(DISTINCT CASE 
                            WHEN MONTH(ir.interview_date) = MONTH(CURRENT_DATE) 
                            THEN ir.id 
                            END) as monthly_interviews,
                        COUNT(DISTINCT ir.id) as total_interviews
                    FROM social_workers sw
                    LEFT JOIN cases c ON sw.id = c.social_worker_id
                    LEFT JOIN interview_records ir ON c.id = ir.case_id  /* 這裡也要修改 */
                    WHERE sw.is_admin = 0
                    GROUP BY sw.id, sw.name
                ");
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td>{$row['case_count']}</td>";
                            echo "<td>{$row['monthly_interviews']}</td>";
                            echo "<td>{$row['total_interviews']}</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>查詢出錯: " . $conn->error . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 確認刪除的彈窗 -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>確認刪除</h3>
            <p>您確定要刪除這個個案嗎？此操作無法復原。</p>
            <div class="modal-buttons">
                <button class="cancel-delete" onclick="closeModal()">取消</button>
                <button class="confirm-delete" onclick="deleteCase()">確認刪除</button>
            </div>
        </div>
    </div>

    <script>
    let currentCaseId = null;

    function confirmDelete(caseId) {
        currentCaseId = caseId;
        Swal.fire({
            title: '確認刪除',
            text: '您確定要刪除這個個案���？此操作無法復原。',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '確認刪除',
            cancelButtonText: '取消',
            background: '#fff',
            borderRadius: '1rem',
        }).then((result) => {
            if (result.isConfirmed) {
                deleteCase();
            }
        });
    }

    function closeModal() {
        modal.style.display = 'none';
        currentCaseId = null;
    }

    function deleteCase() {
        if (!currentCaseId) return;
        
        fetch('delete_case1.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `case_id=${currentCaseId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: data.title,
                    html: `
                        <div style="text-align: left; padding: 10px;">
                            <p style="margin: 0; color: #2c3e50;">${data.message}</p>
                            <p style="margin: 10px 0 0; font-size: 0.9em; color: #666;">
                                刪除時間：${data.details.deleted_at}
                            </p>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: '確定',
                    confirmButtonColor: '#28a745',
                    background: '#fff',
                    borderRadius: '1rem',
                    timer: 3000,
                    timerProgressBar: true
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: data.title,
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: '確定',
                    confirmButtonColor: '#dc3545',
                    background: '#fff',
                    borderRadius: '1rem'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: '系統錯誤',
                text: '刪除操作失敗，請稍後再試',
                icon: 'error',
                confirmButtonText: '確定',
                confirmButtonColor: '#dc3545',
                background: '#fff',
                borderRadius: '1rem'
            });
        });
    }

    // 點擊彈窗外部時關閉彈窗
    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    }

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
                // 發送刪除請求
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
                            // 重新載���頁面以更新列表
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