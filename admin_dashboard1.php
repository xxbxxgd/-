<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login1.php");
    exit();
}
include('header1.php');

// 修正資料庫連線設定
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

// 獲取排序參數
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';

// 驗證排序欄位
$allowed_sort_fields = ['id', 'case_name', 'client_name', 'client_age', 'status'];
if (!in_array($sort, $allowed_sort_fields)) {
    $sort = 'id';
}

// 驗證排序方向
$order = strtolower($order) === 'asc' ? 'asc' : 'desc';

// 獲取系統概況數據
$totalCases = $conn->query("SELECT COUNT(*) as total FROM cases")->fetch_assoc()['total'];
$activeCases = $conn->query("SELECT COUNT(*) as active FROM cases WHERE status = 1")->fetch_assoc()['active'];
$totalSocialWorkers = $conn->query("SELECT COUNT(*) as total FROM social_workers WHERE is_admin = 0")->fetch_assoc()['total'];
$totalInterviews = $conn->query("SELECT COUNT(*) as total FROM interview_records WHERE MONTH(interview_date) = MONTH(CURRENT_DATE())")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理員儀表板</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-container {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-title {
            color: #6c757d;
            font-size: 1rem;
            margin-bottom: 10px;
        }
        .stat-value {
            color: #2c3e50;
            font-size: 2rem;
            font-weight: 700;
        }
        .cases-section {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow-x: auto;
        }
        .section-title {
            color: #2c3e50;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #4CAF50;
        }
        .table {
            margin-bottom: 0;
        }
        .table th, .table td {
            padding: 1rem;
            vertical-align: middle;
        }
        .table th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            white-space: nowrap;
        }
        .btn-action {
            padding: 5px 10px;
            margin: 0 5px;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .table-responsive {
            padding: 0.5rem;
        }
        .table th:nth-child(1), .table td:nth-child(1) { min-width: 80px; }
        .table th:nth-child(2), .table td:nth-child(2) { min-width: 150px; }
        .table th:nth-child(3), .table td:nth-child(3) { min-width: 120px; }
        .table th:nth-child(4), .table td:nth-child(4) { min-width: 80px; }
        .table th:nth-child(5), .table td:nth-child(5) { min-width: 150px; }
        .table th:nth-child(6), .table td:nth-child(6) { min-width: 120px; }
        .table th:nth-child(7), .table td:nth-child(7) { min-width: 100px; }
        .table th:nth-child(8), .table td:nth-child(8) { min-width: 200px; }
    </style>
</head>
<body class="bg-light">
    <div class="dashboard-container">
        <h1 class="mb-4">管理員儀表板</h1>

        <!-- 統計據 -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">
                    <i class="bi bi-folder me-2"></i>總個案數
                </div>
                <div class="stat-value"><?= $totalCases ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">
                    <i class="bi bi-folder-check me-2"></i>進行中個案
                </div>
                <div class="stat-value"><?= $activeCases ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">
                    <i class="bi bi-people me-2"></i>社工人數
                </div>
                <div class="stat-value"><?= $totalSocialWorkers ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">
                    <i class="bi bi-calendar-check me-2"></i>本月訪談社工總次數
                </div>
                <div class="stat-value"><?= $totalInterviews ?></div>
            </div>
        </div>

        <!-- 個案列表 -->
        <div class="cases-section">
            <div class="section-header d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">
                    <i class="bi bi-folder2-open me-2"></i>個案列表
                </h2>
                <button onclick="showAddCaseModal()" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>新增個案
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <a href="?sort=id&order=<?= ($sort == 'id' && $order == 'asc') ? 'desc' : 'asc' ?>" class="text-dark text-decoration-none">
                                    編號
                                    <?php if($sort == 'id'): ?>
                                        <i class="bi bi-arrow-<?= $order == 'asc' ? 'up' : 'down' ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?sort=case_name&order=<?= ($sort == 'case_name' && $order == 'asc') ? 'desc' : 'asc' ?>" class="text-dark text-decoration-none">
                                    個案名稱
                                    <?php if($sort == 'case_name'): ?>
                                        <i class="bi bi-arrow-<?= $order == 'asc' ? 'up' : 'down' ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?sort=client_name&order=<?= ($sort == 'client_name' && $order == 'asc') ? 'desc' : 'asc' ?>" class="text-dark text-decoration-none">
                                    個案姓名
                                    <?php if($sort == 'client_name'): ?>
                                        <i class="bi bi-arrow-<?= $order == 'asc' ? 'up' : 'down' ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?sort=client_age&order=<?= ($sort == 'client_age' && $order == 'asc') ? 'desc' : 'asc' ?>" class="text-dark text-decoration-none">
                                    年齡
                                    <?php if($sort == 'client_age'): ?>
                                        <i class="bi bi-arrow-<?= $order == 'asc' ? 'up' : 'down' ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>聯絡方式</th>
                            <th>負責社工</th>
                            <th>狀態</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT 
                                c.id,
                                c.case_name,
                                COALESCE(ci.client_name, c.case_name) as client_name,
                                COALESCE(ci.client_age, 0) as client_age,
                                COALESCE(ci.client_contact, '未提供') as client_contact,
                                sw.name as social_worker_name,
                                c.status
                            FROM cases c
                            LEFT JOIN `case information` ci ON c.case_name = ci.case_name
                            LEFT JOIN social_workers sw ON c.social_worker_id = sw.id
                            ORDER BY $sort $order";
                        
                        $result = $conn->query($sql);
                        
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo '<tr>
                                    <td>' . $row['id'] . '</td>
                                    <td>' . $row['case_name'] . '</td>
                                    <td>' . $row['client_name'] . '</td>
                                    <td>' . $row['client_age'] . '</td>
                                    <td>' . $row['client_contact'] . '</td>
                                    <td>' . ($row['social_worker_name'] ?? '未分配') . '</td>
                                    <td>
                                        <span class="status-badge ' . ($row['status'] ? 'status-active' : 'status-inactive') . '">
                                            ' . ($row['status'] ? '進行中' : '已結束') . '
                                        </span>
                                    </td>
                                    <td>';
                                if ($row['status'] == 1) {
                                    echo '<button class="btn btn-sm btn-primary me-2" onclick="editCase(' . $row['id'] . ')">
                                            <i class="bi bi-pencil me-1"></i>編輯
                                          </button>
                                          <button class="btn btn-sm btn-danger me-2" onclick="deleteCase(' . $row['id'] . ')">
                                            <i class="bi bi-trash me-1"></i>刪除
                                          </button>
                                          <button class="btn btn-sm btn-success" onclick="showAssignModal(' . $row['id'] . ')">
                                            <i class="bi bi-person-plus me-1"></i>分配
                                          </button>';
                                }
                                echo '</td>
                                </tr>';
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>目前沒��個案記錄</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    function deleteCase(caseId) {
        Swal.fire({
            title: '確認刪除',
            text: '您確定要刪除這個個案嗎？此操作無法復原。',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '確認刪除',
            cancelButtonText: '取消'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('delete_case1.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'case_id=' + caseId
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

    function editCase(caseId) {
        // 獲取個案資料
        fetch(`get_case_data.php?id=${caseId}`)
            .then(response => response.json())
            .then(data => {
                // 填充表單
                document.getElementById('edit_case_id').value = data.id;
                document.getElementById('edit_case_name').value = data.case_name;
                document.getElementById('edit_client_name').value = data.client_name;
                document.getElementById('edit_client_age').value = data.client_age;
                document.getElementById('edit_client_contact').value = data.client_contact;
                
                // 設置狀態
                if (data.status == 1) {
                    document.getElementById('edit_status_active').checked = true;
                } else {
                    document.getElementById('edit_status_inactive').checked = true;
                }
                
                // 顯示 Modal
                new bootstrap.Modal(document.getElementById('editCaseModal')).show();
            });
    }

    function updateCase() {
        const formData = new FormData(document.getElementById('editCaseForm'));
        
        fetch('update_case.php', {
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

    // 添加新增個案按鈕的點擊事件處理
    function showAddCaseModal() {
        new bootstrap.Modal(document.getElementById('addCaseModal')).show();
    }

    // 提交新個案的函數
    function submitNewCase() {
        const formData = new FormData(document.getElementById('addCaseForm'));
        
        fetch('add_case.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: '新增成功！',
                    text: data.message,
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: '新增失敗',
                    text: data.message,
                    icon: 'error'
                });
            }
        });
    }

    function showAssignModal(caseId) {
        document.getElementById('assign_case_id').value = caseId;
        new bootstrap.Modal(document.getElementById('assignModal')).show();
    }

    function assignCase() {
        const formData = new FormData(document.getElementById('assignForm'));
        
        fetch('assign_case_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: '分配成功！',
                    text: data.message,
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: '分配失敗',
                    text: data.message,
                    icon: 'error'
                });
            }
        });
    }
    </script>

    <!-- Edit Case Modal -->
    <div class="modal fade" id="editCaseModal" tabindex="-1" aria-labelledby="editCaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCaseModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>編輯個案資料
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCaseForm">
                        <input type="hidden" id="edit_case_id" name="case_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="edit_case_name">
                                        <i class="bi bi-folder2 me-2"></i>個案名稱
                                    </label>
                                    <input type="text" class="form-control" id="edit_case_name" name="case_name" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="edit_client_name">
                                        <i class="bi bi-person me-2"></i>個案姓名
                                    </label>
                                    <input type="text" class="form-control" id="edit_client_name" name="client_name" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="edit_client_age">
                                        <i class="bi bi-calendar3 me-2"></i>個案年齡
                                    </label>
                                    <input type="number" class="form-control" id="edit_client_age" name="client_age" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="edit_client_contact">
                                        <i class="bi bi-telephone me-2"></i>聯絡方式
                                    </label>
                                    <input type="text" class="form-control" id="edit_client_contact" name="client_contact" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">
                                <i class="bi bi-check-circle me-2"></i>個案狀態
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="edit_status_active" value="1">
                                <label class="form-check-label" for="edit_status_active">進行中</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="edit_status_inactive" value="0">
                                <label class="form-check-label" for="edit_status_inactive">已結束</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="updateCase()">更新資料</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Case Modal -->
    <div class="modal fade" id="addCaseModal" tabindex="-1" aria-labelledby="addCaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCaseModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>新增個案
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCaseForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="add_case_name">
                                        <i class="bi bi-folder2 me-2"></i>個案名稱
                                    </label>
                                    <input type="text" class="form-control" id="add_case_name" name="case_name" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="add_client_name">
                                        <i class="bi bi-person me-2"></i>個案姓名
                                    </label>
                                    <input type="text" class="form-control" id="add_client_name" name="client_name" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="add_client_age">
                                        <i class="bi bi-calendar3 me-2"></i>個案年齡
                                    </label>
                                    <input type="number" class="form-control" id="add_client_age" name="client_age" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="add_client_contact">
                                        <i class="bi bi-telephone me-2"></i>聯絡方式
                                    </label>
                                    <input type="text" class="form-control" id="add_client_contact" name="client_contact" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="submitNewCase()">新增個案</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Case Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">
                        <i class="bi bi-person-plus-fill me-2"></i>分配個案給社工
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignForm">
                        <input type="hidden" id="assign_case_id" name="case_id">
                        <div class="form-group mb-3">
                            <label class="form-label" for="social_worker_id">
                                <i class="bi bi-person-badge me-2"></i>選擇社工
                            </label>
                            <select class="form-select" id="social_worker_id" name="social_worker_id" required>
                                <option value="">請選擇社工...</option>
                                <?php
                                $sw_result = $conn->query("SELECT id, name FROM social_workers");
                                while ($sw = $sw_result->fetch_assoc()) {
                                    echo "<option value='{$sw['id']}'>{$sw['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="assignCase()">確定分配</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>