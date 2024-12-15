<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>社工資料管理系統</title>
    <style>
    /* 導覽列樣式 */
    .navbar {
        background-color: #2c3e50;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        width: 100%;
        box-sizing: border-box;
        position: relative;
    }

    .navbar-logo {
        color: white;
        text-decoration: none;
        font-size: 20px;
        font-weight: bold;
    }

    .navbar-menu {
        display: flex;
        gap: 20px;
        align-items: center;  /* 確保所有項目垂直置中 */
    }

    .navbar-menu a {
        color: white;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 4px;
        transition: background-color 0.3s;
        font-size: 16px;     /* 統一字體大小 */
        white-space: nowrap; /* 防止文字換行 */
    }

    .navbar-menu a:hover {
        background-color: #34495e;
    }

    /* 移除任何可能的外邊距 */
    body {
        margin: 0;
        padding: 0;
    }

    /* 確保導覽列下方內容正確定位 */
    .content {
        margin-top: 20px;
    }

    /* 歡迎區域樣式 */
    .welcome-content {  /* 改名以避免衝突 */
        text-align: center;
        padding: 60px 20px;
        background: linear-gradient(to bottom, #f8f9fa, #ffffff);
        border-radius: 10px;
        margin: 20px auto;
        max-width: 1000px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .welcome-content h1 {
        font-size: 36px;
        color: #2c3e50;
        margin-bottom: 20px;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    .welcome-content p {
        font-size: 20px;
        color: #666;
        line-height: 1.6;
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* 添加簡單的動畫效果 */
    .welcome-content h1, 
    .welcome-content p {
        animation: fadeIn 1s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<!-- 導覽列 -->
<div class="navbar">
    <a href="" class="navbar-logo" aria-label="首頁">社工管理系統</a>
    <div class="navbar-menu">
        <a href="index1.php" aria-label="首頁">首頁</a>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login1.php" aria-label="登入">登入</a>
        <?php else: ?>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <a href="admin_dashboard1.php" aria-label="管理員儀表板">管理員儀表板</a>
                <a href="view_all_records1.php" aria-label="所有紀錄">所有紀錄</a>
            <?php endif; ?>
            <a href="interview_record1.php" aria-label="新增訪談紀錄">新增訪談紀錄</a>
            <a href="my_cases1.php" aria-label="我的個案">我的個案</a>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <a href="manage_users1.php">用戶管理</a>
            <?php endif; ?>
            <a href="profile1.php">個人資料</a>
            <a href="logout1.php" aria-label="登出">登出</a>
        <?php endif; ?>
    </div>
</div>

<!-- 歡迎區域 -->
<div class="welcome-content">
    <h1>歡迎來到社工資料管理系統</h1>
    <p>在這裡，您可以管理社工帳號、新增個案及記錄訪談。</p>
</div>
</body>
</html>