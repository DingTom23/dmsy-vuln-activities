<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // SQL注入漏洞点 - 故意不使用预处理语句和过滤
    // 提示: 尝试 ' OR '1'='1 作为用户名和密码
    // 或者 admin' -- 作为用户名，任意密码
    $sql = "SELECT id, username, password, role FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: dashboard.php");
        exit;
    } else {
        $error = '用户名或密码不正确';
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - Code Academy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="particles-container" id="particles-js"></div>
    
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-code"></i>
                <span>Code Academy</span>
            </div>
            <h2>用户登录</h2>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form class="login-form" method="POST" action="login.php">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i>
                    <span>用户名</span>
                </label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i>
                    <span>密码</span>
                </label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn primary-btn">
                    <span>登录</span>
                    <i class="fas fa-sign-in-alt"></i>
                </button>
            </div>
            
            <div class="form-footer">
                <p>还没有账号? <a href="#">联系管理员</a></p>
            </div>
        </form>
        
        <div class="back-to-home">
            <a href="index.php" class="btn secondary-btn">
                <i class="fas fa-home"></i>
                <span>返回首页</span>
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html> 