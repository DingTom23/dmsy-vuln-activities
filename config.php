<?php
// 数据库连接配置
$db_host = 'localhost';
$db_user = 'dmsyctfuser';
$db_pass = 'dmsyctfpassword';
$db_name = 'coolsite';

// 创建数据库连接
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// 检查连接是否成功
if (!$conn) {
    die("连接失败: " . mysqli_connect_error());
}

// 设置字符集
mysqli_set_charset($conn, "utf8mb4");
?> 