<?php
// 安装脚本 - 用于一键搭建 SQL 环境

// 设置页面编码
header('Content-Type: text/html; charset=utf-8');

// 显示错误信息（生产环境中应关闭）
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 数据库配置信息
$db_host = 'localhost';    // 数据库服务器地址
$db_user = 'root';         // MySQL root 用户名
$db_pass = '$1$0kRGbLi5$o1jTxIVwcDWSOcKtTLukM1';             // MySQL root 密码（默认为空，请根据实际情况修改）
$db_name = 'coolsite';     // 要创建的数据库名称
$new_user = 'dmsyctfuser'; // 要创建的新用户名
$new_pass = 'dmsyctfpassword'; // 新用户的密码

// 数据库文件路径
$sql_file = 'database.sql';

// HTML 头部
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CTF 网站安全挑战 - 安装程序</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .success {
            color: green;
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 3px;
            margin: 10px 0;
        }
        .error {
            color: red;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 3px;
            margin: 10px 0;
        }
        .info {
            color: #0277bd;
            background-color: #e1f5fe;
            padding: 10px;
            border-radius: 3px;
            margin: 10px 0;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 3px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CTF 网站安全挑战 - 安装程序</h1>';

// 检查是否已提交表单
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单提交的数据库信息
    if (isset($_POST['db_user'])) {
        $db_user = $_POST['db_user'];
    }
    if (isset($_POST['db_pass'])) {
        $db_pass = $_POST['db_pass'];
    }
    
    // 连接数据库服务器
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass);
        
        // 检查连接
        if ($conn->connect_error) {
            throw new Exception("连接数据库失败: " . $conn->connect_error);
        }
        
        echo '<div class="success">成功连接到 MySQL 服务器！</div>';
        
        // 创建数据库
        $sql = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if ($conn->query($sql) === TRUE) {
            echo '<div class="success">数据库 "' . $db_name . '" 创建成功！</div>';
        } else {
            throw new Exception("创建数据库失败: " . $conn->error);
        }
        
        // 创建新用户并授权
        // 先检查用户是否存在，如果存在则删除
        $sql = "SELECT user FROM mysql.user WHERE user = '$new_user'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $sql = "DROP USER '$new_user'@'localhost'";
            if ($conn->query($sql) === TRUE) {
                echo '<div class="info">已删除现有用户 "' . $new_user . '"</div>';
            } else {
                throw new Exception("删除现有用户失败: " . $conn->error);
            }
        }
        
        // 创建新用户
        $sql = "CREATE USER '$new_user'@'localhost' IDENTIFIED BY '$new_pass'";
        if ($conn->query($sql) === TRUE) {
            echo '<div class="success">用户 "' . $new_user . '" 创建成功！</div>';
        } else {
            throw new Exception("创建用户失败: " . $conn->error);
        }
        
        // 授予权限
        $sql = "GRANT ALL PRIVILEGES ON `$db_name`.* TO '$new_user'@'localhost'";
        if ($conn->query($sql) === TRUE) {
            echo '<div class="success">已授予用户 "' . $new_user . '" 对数据库 "' . $db_name . '" 的所有权限！</div>';
        } else {
            throw new Exception("授予权限失败: " . $conn->error);
        }
        
        // 刷新权限
        $sql = "FLUSH PRIVILEGES";
        if ($conn->query($sql) === TRUE) {
            echo '<div class="success">权限刷新成功！</div>';
        } else {
            throw new Exception("刷新权限失败: " . $conn->error);
        }
        
        // 切换到新创建的数据库
        $conn->select_db($db_name);
        
        // 检查SQL文件是否存在
        if (file_exists($sql_file)) {
            // 读取SQL文件内容
            $sql_content = file_get_contents($sql_file);
            
            if ($sql_content) {
                // 执行SQL语句
                if ($conn->multi_query($sql_content)) {
                    echo '<div class="success">数据库结构和初始数据导入成功！</div>';
                    
                    // 处理所有结果集
                    do {
                        if ($result = $conn->store_result()) {
                            $result->free();
                        }
                    } while ($conn->more_results() && $conn->next_result());
                } else {
                    throw new Exception("导入数据失败: " . $conn->error);
                }
            } else {
                throw new Exception("无法读取SQL文件内容");
            }
        } else {
            echo '<div class="error">SQL文件 "' . $sql_file . '" 不存在！请确保该文件位于与本安装脚本相同的目录中。</div>';
        }
        
        // 检查uploads目录是否存在，如果不存在则创建
        if (!file_exists('uploads')) {
            if (mkdir('uploads', 0777, true)) {
                echo '<div class="success">uploads 目录创建成功！</div>';
            } else {
                echo '<div class="error">无法创建 uploads 目录，请手动创建并设置权限为 777</div>';
            }
        } else {
            // 设置目录权限
            if (chmod('uploads', 0777)) {
                echo '<div class="success">uploads 目录权限设置成功！</div>';
            } else {
                echo '<div class="error">无法设置 uploads 目录权限，请手动设置权限为 777</div>';
            }
        }
        
        // 创建或更新config.php文件
        $config_content = '<?php
// 数据库连接配置
$db_host = \'' . $db_host . '\';    // 数据库服务器地址
$db_user = \'' . $new_user . '\';   // 数据库用户名
$db_pass = \'' . $new_pass . '\';   // 数据库密码
$db_name = \'' . $db_name . '\';    // 数据库名称

// 创建数据库连接
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 设置字符集
$conn->set_charset("utf8mb4");
?>';

        if (file_put_contents('config.php', $config_content)) {
            echo '<div class="success">config.php 文件创建/更新成功！</div>';
        } else {
            echo '<div class="error">无法创建/更新 config.php 文件，请手动创建</div>';
        }
        
        echo '<div class="success">
            <h2>安装完成！</h2>
            <p>CTF 网站安全挑战环境已成功搭建。</p>
            <p>您现在可以访问 <a href="index.php">网站首页</a> 开始挑战。</p>
            <p>默认用户:</p>
            <ul>
                <li>管理员: admin / _iloveyou_comet$</li>
                <li>普通用户: user1 / password</li>
                <li>普通用户: zhangsan / qwerty</li>
                <li>普通用户: test / test</li>
            </ul>
        </div>';
        
        // 关闭数据库连接
        $conn->close();
        
    } catch (Exception $e) {
        echo '<div class="error">' . $e->getMessage() . '</div>';
    }
} else {
    // 显示安装表单
    echo '
    <div class="info">
        <p>此安装程序将自动完成以下操作：</p>
        <ol>
            <li>创建数据库 "' . $db_name . '"</li>
            <li>创建数据库用户 "' . $new_user . '"</li>
            <li>导入数据库结构和初始数据</li>
            <li>创建并设置 uploads 目录权限</li>
            <li>创建/更新 config.php 配置文件</li>
        </ol>
    </div>
    
    <form method="post" action="">
        <h3>数据库连接信息</h3>
        <p>
            <label for="db_user">MySQL 管理员用户名:</label><br>
            <input type="text" id="db_user" name="db_user" value="' . htmlspecialchars($db_user) . '" required>
        </p>
        <p>
            <label for="db_pass">MySQL 管理员密码:</label><br>
            <input type="password" id="db_pass" name="db_pass" value="' . htmlspecialchars($db_pass) . '">
        </p>
        <p>
            <input type="submit" value="开始安装" class="btn">
        </p>
    </form>';
}

// HTML 尾部
echo '
    </div>
</body>
</html>';
?>