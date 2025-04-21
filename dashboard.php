<?php
session_start();
require_once 'config.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// 设置flag，只有admin才能看到
$first_flag = '';
if ($username === 'admin' && $role === 'admin') {
    $first_flag = "CTF{W3lc0m3_4dm1n_Y0u_F0und_Th3_F1r5t_Fl4g}";
}

// 处理文件上传（仅限admin）
if (isset($_POST['upload']) && $role === 'admin') {
    $target_dir = "uploads/";
    
    // 创建上传目录（如果不存在）
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    
    // 漏洞：没有检查文件类型，可以上传任何文件
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $message = "文件 ". htmlspecialchars(basename($_FILES["fileToUpload"]["name"])). " 已成功上传。";
    } else {
        $error = "抱歉，上传文件时出错。";
    }
}

// 处理密码修改（仅限admin）
if (isset($_POST['change_password']) && $role === 'admin') {
    $target_user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];
    
    // 直接使用明文密码
    $sql = "UPDATE users SET password = '$new_password' WHERE id = $target_user_id";
    
    if (mysqli_query($conn, $sql)) {
        $message = "密码修改成功！";
    } else {
        $error = "密码修改失败：" . mysqli_error($conn);
    }
}

// 获取所有用户（仅限admin）
$users = array();
if ($role === 'admin') {
    $sql = "SELECT id, username, password, role FROM users";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>控制面板 - Code Academy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dashboard-page">
    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-code"></i>
                    <span>Code Academy</span>
                </div>
                <button id="sidebar-toggle" class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-details">
                    <h3><?php echo htmlspecialchars($username); ?></h3>
                    <span><?php echo htmlspecialchars($role); ?></span>
                </div>
            </div>
            
            <!-- 简化的功能菜单 -->
            <div class="dashboard-menu">
                <a href="#dashboard" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>控制台</span>
                </a>
                
                <?php if ($role === 'admin'): ?>
                <a href="#upload" class="menu-item">
                    <i class="fas fa-upload"></i>
                    <span>文件上传</span>
                </a>
                
                <a href="#users" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>用户管理</span>
                </a>
                
                <a href="#flag" class="menu-item">
                    <i class="fas fa-flag"></i>
                    <span>Flag信息</span>
                </a>
                <?php endif; ?>
                
                <a href="#profile" class="menu-item">
                    <i class="fas fa-user"></i>
                    <span>个人资料</span>
                </a>
                
                <a href="logout.php" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>退出登录</span>
                </a>
            </div>
        </aside>
        
        <main class="main-content">
            <header class="dashboard-header">
                <div class="page-title">
                    <h1>用户控制台</h1>
                </div>
                
                <div class="header-actions">
                    <a href="index.php" class="btn small-btn">
                        <i class="fas fa-home"></i>
                        <span>网站首页</span>
                    </a>
                    <a href="logout.php" class="btn small-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>退出</span>
                    </a>
                </div>
            </header>
            
            <div class="dashboard-content">
                <?php if ($message): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <div id="dashboard" class="dashboard-section">
                    <div class="welcome-banner">
                        <h2>欢迎回来, <?php echo htmlspecialchars($username); ?>!</h2>
                        <p>今天是 <?php echo date('Y年m月d日'); ?></p>
                        
                        <?php if ($username === 'admin' && $role === 'admin'): ?>
                        <div class="flag-alert">
                            <p>恭喜你！作为管理员，这是你的第一个Flag：</p>
                            <div class="flag-container"><?php echo $first_flag; ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- 控制台卡片 -->
                    <div class="dashboard-cards">
                        <div class="dashboard-card">
                            <div class="card-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="card-content">
                                <h3>我的课程</h3>
                                <p>查看已报名课程和学习进度</p>
                                <a href="#" class="btn small-btn">进入</a>
                            </div>
                        </div>
                        
                        <div class="dashboard-card">
                            <div class="card-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="card-content">
                                <h3>作业任务</h3>
                                <p>管理和提交课程作业</p>
                                <a href="#" class="btn small-btn">进入</a>
                            </div>
                        </div>
                        
                        <div class="dashboard-card">
                            <div class="card-icon">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <div class="card-content">
                                <h3>我的证书</h3>
                                <p>查看获得的课程证书</p>
                                <a href="#" class="btn small-btn">进入</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 仅管理员可见的Flag部分 -->
                <?php if ($role === 'admin'): ?>
                <div id="flag" class="dashboard-section">
                    <div class="section-title">
                        <h2>Flag信息</h2>
                    </div>
                    
                    <div class="flag-info">
                        <div class="flag-detail">
                            <h3>第一个Flag</h3>
                            <div class="flag-value"><?php echo $first_flag; ?></div>
                            <p>恭喜你获得第一个flag！你已经成功以管理员身份登录。</p>
                        </div>
                        <div class="flag-detail">
                            <h3>第二个Flag</h3>
                            <div class="flag-hint">
                                <p>提示：尝试上传一个PHP Webshell，并提权到 user</p>
                                <p>位置：/home/{user}/user.txt </p>
                            </div>
                        </div>
                        <div class="flag-detail">
                            <h3>最终Flag</h3>
                            <div class="flag-hint">
                                <p>提示：尝试获取服务器 root 权限后获得。</p>
                                <p>位置：/root/root.txt </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 文件上传部分 - 仅管理员可见 -->
                <div id="upload" class="dashboard-section">
                    <div class="section-title">
                        <h2>文件上传</h2>
                    </div>
                    
                    <div class="upload-container">
                        <form method="post" enctype="multipart/form-data" class="upload-form">
                            <div class="form-group">
                                <label for="fileToUpload">选择要上传的文件：</label>
                                <input type="file" name="fileToUpload" id="fileToUpload">
                            </div>
                            <div class="form-actions">
                                <button type="submit" name="upload" class="btn primary-btn">
                                    <i class="fas fa-upload"></i>
                                    <span>上传文件</span>
                                </button>
                            </div>
                            <div class="upload-note">
                                <p>提示：支持大多数文件类型，没有严格限制。</p>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- 管理员用户管理部分 -->
                <div id="users" class="dashboard-section">
                    <div class="section-title">
                        <h2>用户管理</h2>
                    </div>
                    
                    <div class="users-table-container">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>用户名</th>
                                    <th>密码（明文）</th>
                                    <th>角色</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['password']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td>
                                        <button class="btn small-btn" onclick="showPasswordForm(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                            <i class="fas fa-key"></i> 修改密码
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- 修改密码表单 -->
                    <div id="passwordModal" class="modal">
                        <div class="modal-content">
                            <span class="close-btn" onclick="closePasswordForm()">&times;</span>
                            <h3>修改用户密码</h3>
                            <form method="post" id="changePasswordForm">
                                <input type="hidden" name="user_id" id="user_id">
                                <div class="form-group">
                                    <label for="username_display">用户名：</label>
                                    <input type="text" id="username_display" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="new_password">新密码：</label>
                                    <input type="text" name="new_password" id="new_password" required>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" name="change_password" class="btn primary-btn">
                                        <i class="fas fa-save"></i>
                                        <span>保存</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- 个人资料部分 - 所有用户可见 -->
                <div id="profile" class="dashboard-section">
                    <div class="section-title">
                        <h2>个人资料</h2>
                    </div>
                    
                    <div class="profile-info">
                        <div class="profile-item">
                            <span class="profile-label">用户名：</span>
                            <span class="profile-value"><?php echo htmlspecialchars($username); ?></span>
                        </div>
                        <div class="profile-item">
                            <span class="profile-label">用户角色：</span>
                            <span class="profile-value"><?php echo htmlspecialchars($role); ?></span>
                        </div>
                        <div class="profile-item">
                            <span class="profile-label">上次登录：</span>
                            <span class="profile-value"><?php echo date('Y-m-d H:i:s'); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- 服务器信息部分 -->
                <div class="dashboard-section">
                    <div class="section-title">
                        <h2>服务器信息</h2>
                    </div>
                    
                    <div class="server-info">
                        <div class="info-item">
                            <span class="info-label">服务器系统：</span>
                            <span class="info-value"><?php echo php_uname(); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">PHP版本：</span>
                            <span class="info-value"><?php echo phpversion(); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Web服务器：</span>
                            <span class="info-value"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span>
                        </div>
                        <!-- 隐藏的flag提示 -->
                        <div class="info-item" style="color: #f0f0f0; font-size: 0.8em;">
                            <span class="info-label">Flag提示：</span>
                            <span class="info-value">尝试登录 admin 管理员 账户来获得第一个 flag </span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
    // 修改密码表单的JavaScript
    function showPasswordForm(userId, username) {
        document.getElementById('user_id').value = userId;
        document.getElementById('username_display').value = username;
        document.getElementById('passwordModal').style.display = 'block';
    }
    
    function closePasswordForm() {
        document.getElementById('passwordModal').style.display = 'none';
    }
    
    // 点击模态框外部时关闭
    window.onclick = function(event) {
        var modal = document.getElementById('passwordModal');
        if (event.target == modal) {
            closePasswordForm();
        }
    }
    
    // 菜单项点击处理
    document.addEventListener('DOMContentLoaded', function() {
        const menuItems = document.querySelectorAll('.menu-item');
        
        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // 移除所有active类
                menuItems.forEach(i => i.classList.remove('active'));
                // 添加active类到点击的项目
                this.classList.add('active');
            });
        });
    });
    </script>
    
    <script src="js/dashboard.js"></script>
</body>
</html> 