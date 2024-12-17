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

        /* 添加搜尋框樣式 */
        .search-container {
            margin-bottom: 20px;
        }

        .input-group {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .input-group .form-control {
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 14px;
        }

        .input-group .form-control:focus {
            border-color: #b69d74;
            box-shadow: 0 0 0 0.2rem rgba(182, 157, 116, 0.15);
        }

        .input-group .btn-primary {
            background: linear-gradient(45deg, #d4c19c, #b69d74);
            border: none;
            padding: 0 20px;
            color: white;
            font-size: 14px;
        }

        .input-group .btn-primary:hover {
            background: linear-gradient(45deg, #c4b08b, #a68c63);
        }

        /* 清除浮動 */
        .col-12::after {
            content: "";
            display: table;
            clear: both;
        }

        .input-group {
            display: flex;
        }

        .input-group .form-control {
            border: 1px solid #ccc;
            border-right: none;
            border-radius: 4px 0 0 4px;
        }

        .input-group .btn-primary {
            border-radius: 0 4px 4px 0;
            padding: 4px 12px;
            background-color: #0d6efd;
            border: 1px solid #0d6efd;
        }

        .search-box {
            margin-bottom: 10px;
        }

        .input-group {
            display: flex;
        }

        .input-group .form-control {
            border-radius: 4px 0 0 4px;
        }

        .btn-search {
            height: 35px;
            padding: 4px 12px;
            background-color: #0d6efd;
            border: 1px solid #0d6efd;
            color: white;
            border-radius: 0 4px 4px 0;
            display: flex;
            align-items: center;
        }

        .btn-search:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
            color: white;
        }

        .search-box {
            margin: 0;
        }

        /* 確保標題和搜尋框的對齊 */
        .section h2 {
            font-size: 24px;
            line-height: 1.2;
        }

        .table-container {
            position: relative;
            margin-top: 50px; /* 為搜尋框留出空間 */
        }

        .search-box {
            position: absolute;
            top: -45px;
            right: 0;
        }

        .input-group {
            display: flex;
        }

        .input-group .form-control {
            border-radius: 4px 0 0 4px;
        }

        .btn-search {
            height: 35px;
            padding: 4px 12px;
            background-color: #0d6efd;
            border: 1px solid #0d6efd;
            color: white;
            border-radius: 0 4px 4px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-search:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
            color: white;
        }

        .input-group {
            display: flex;
        }

        .input-group .form-control {
            border-radius: 4px 0 0 4px;
        }

        .btn-search {
            padding: 4px 12px;
            background-color: #0d6efd;
            border: 1px solid #0d6efd;
            color: white;
            border-radius: 0 4px 4px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            width: 70px;
        }

        .btn-search:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
            color: white;
        }

        .search-box {
            margin: 0;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="records-container">
        <div class="container">
            <h1>所有紀錄</h1>
            
            <div class="section">
                <h2>個案分配紀錄</h2>
                
                <!-- 添加搜尋框 -->
                <div class="search-container mb-4">
                    <form method="GET" class="row justify-content-end">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="搜尋個案名稱..." 
                                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i>搜尋
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

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
                        // 修改查詢以支援搜尋
                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $sql = "
                            SELECT c.id, c.case_name, sw.name as social_worker_name, 
                                   c.created_at, c.status
                            FROM cases c 
                            LEFT JOIN social_workers sw ON c.social_worker_id = sw.id
                            WHERE c.case_name LIKE ?
                            ORDER BY c.created_at ASC
                        ";
                        
                        $stmt = $conn->prepare($sql);
                        $searchTerm = "%$search%";
                        $stmt->bind_param("s", $searchTerm);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
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
                            echo "<tr><td colspan='6'>沒有找到相關個案</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

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
                        FROM interview_records ir  /* 修改這裡：從 interview 改為 interview_records */
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
            text: '您確定要刪除這個個案嗎？此操作無法復原。',
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
                // 發送刪���請求
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
                            // 新載頁面以更新列表
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