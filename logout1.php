<?php
session_start();
session_unset();  // 清除會話變數
session_destroy(); // 銷毀會話
header("Location: login1.php"); // 重定向到登入頁面
exit();
?>