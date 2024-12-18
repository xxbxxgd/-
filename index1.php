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

// 獲取系統概況數據
$totalCases = 0;
$activeCases = 0;
$totalSocialWorkers = 0;
$totalInterviews = 0;

if ($_SESSION['is_admin']) {
    // 查詢總個案數
    $result = $conn->query("SELECT COUNT(*) as total FROM cases");
    $totalCases = $result->fetch_assoc()['total'];

    // 查詢進行中的個案數
    $result = $conn->query("SELECT COUNT(*) as active FROM cases WHERE status = 1");
    $activeCases = $result->fetch_assoc()['active'];

    // 查詢社工人數（不包括管理員）
    $result = $conn->query("SELECT COUNT(*) as total FROM social_workers WHERE is_admin = 0");
    $totalSocialWorkers = $result->fetch_assoc()['total'];

    // 查詢本月訪談次數
    $result = $conn->query("SELECT COUNT(*) as total FROM interview_records WHERE MONTH(interview_date) = MONTH(CURRENT_DATE())");
    $totalInterviews = $result->fetch_assoc()['total'];
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>社工資料管理系統</title>
    <style>
    .welcome-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
    }

    .welcome-alert {
        background-color: #d4edda;
        color: #155724;
        padding: 15px 20px;
        border-radius: 8px;
        text-align: center;
        font-size: 18px;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-align: center;
    }

    .stat-card h3 {
        color: #6c757d;
        margin-bottom: 10px;
        font-size: 16px;
    }

    .stat-card .number {
        color: #2c3e50;
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .features-section {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .features-section h2 {
        color: #2c3e50;
        font-size: 24px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: bold;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .feature-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        text-decoration: none;
        transition: transform 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        background: #e9ecef;
    }

    .feature-card h3 {
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 18px;
    }

    .feature-card p {
        color: #6c757d;
        margin: 0;
        font-size: 14px;
    }
</style>
</head>
<body>
<div class="welcome-container">
    <div class="welcome-alert">
        歡迎回來，<?php echo htmlspecialchars($_SESSION['name']); ?>！
    </div>

    <?php if ($_SESSION['is_admin']): ?>
    <!-- 管理員儀表板統計 -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>總個案數</h3>
            <div class="number"><?php echo $totalCases; ?></div>
        </div>
        <div class="stat-card">
            <h3>進行中個案</h3>
            <div class="number"><?php echo $activeCases; ?></div>
        </div>
        <div class="stat-card">
            <h3>社工人數</h3>
            <div class="number"><?php echo $totalSocialWorkers; ?></div>
        </div>
        <div class="stat-card">
            <h3>本月訪談次數</h3>
            <div class="number"><?php echo $totalInterviews; ?></div>
        </div>
    </div>

    <div class="features-section">
        <h2>管理功能</h2>
        <div class="features-grid">
            <a href="assign_case1.php" class="feature-card">
                <h3>個案分配</h3>
                <p>分配新個案給社工</p>
            </a>
            <a href="view_all_records1.php" class="feature-card">
                <h3>所有紀錄</h3>
                <p>查看所有個案和訪談紀錄</p>
            </a>
            <a href="register1.php" class="feature-card">
                <h3>新增社工</h3>
                <p>註冊新的社工帳號</p>
            </a>
            <a href="manage_users1.php" class="feature-card">
                <h3>用戶管理</h3>
                <p>管理社工帳號和權限</p>
            </a>
        </div>
    </div>

    <?php else: ?>
    <!-- 一般社工的功能列表 -->
    <div class="features-section">
        <h2>系統功能</h2>
        <div class="features-grid">
            <a href="add1.php" class="feature-card">
                <h3>新增個案</h3>
                <p>新增個案基本資料</p>
            </a>
            <a href="interview_record1.php" class="feature-card">
                <h3>新增訪談紀錄</h3>
                <p>記錄個案訪談內容</p>
            </a>
            <a href="my_cases1.php" class="feature-card">
                <h3>我的個案</h3>
                <p>查看個案資料及訪談紀錄</p>
            </a>
            <a href="profile1.php" class="feature-card">
                <h3>個人資料</h3>
                <p>修改個人帳號資料</p>
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>