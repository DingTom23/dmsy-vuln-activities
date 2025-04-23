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
    $first_flag = "flag{69dc7238-202a-11f0-b2fe-000c29094b2d}";
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
                
                <a href="games.php" class="menu-item">
                    <i class="fas fa-gamepad"></i>
                    <span>游戏中心</span>
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
                                <i class="fas fa-gamepad"></i>
                            </div>
                            <div class="card-content">
                                <h3>游戏中心</h3>
                                <p>参与有趣的编程游戏</p>
                                <a href="games.php" class="btn small-btn">进入</a>
                            </div>
                        </div>
                        
                        <div class="dashboard-card">
                            <div class="card-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="card-content">
                                <h3>成就</h3>
                                <p>查看你的学习成就和徽章</p>
                                <a href="#" class="btn small-btn">进入</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 文件上传区域 -->
                <?php if ($role === 'admin'): ?>
                <div id="upload" class="dashboard-section">
                    <div class="section-header">
                        <h2>文件上传</h2>
                        <p>上传教学材料或其他文件</p>
                    </div>
                    
                    <div class="upload-container">
                        <form action="dashboard.php#upload" method="POST" enctype="multipart/form-data" class="upload-form">
                            <div class="form-group">
                                <label for="fileToUpload">选择文件：</label>
                                <input type="file" name="fileToUpload" id="fileToUpload" required>
                            </div>
                            
                            <button type="submit" name="upload" class="btn primary-btn">
                                <i class="fas fa-upload"></i>
                                <span>上传文件</span>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- 用户管理区域 -->
                <div id="users" class="dashboard-section">
                    <div class="section-header">
                        <h2>用户管理</h2>
                        <p>管理系统用户</p>
                    </div>
                    
                    <div class="users-table-container">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>用户名</th>
                                    <th>密码</th>
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
                                        <button class="btn small-btn change-password-btn" data-user-id="<?php echo $user['id']; ?>">
                                            <i class="fas fa-key"></i>
                                            <span>修改密码</span>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- 修改密码模态框 -->
                    <div id="passwordModal" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3>修改用户密码</h3>
                                <span class="close-modal">&times;</span>
                            </div>
                            
                            <div class="modal-body">
                                <form action="dashboard.php#users" method="POST" class="password-form">
                                    <input type="hidden" name="user_id" id="user_id">
                                    
                                    <div class="form-group">
                                        <label for="new_password">新密码：</label>
                                        <input type="password" name="new_password" id="new_password" required>
                                    </div>
                                    
                                    <button type="submit" name="change_password" class="btn primary-btn">
                                        <i class="fas fa-save"></i>
                                        <span>保存更改</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Flag信息 -->
                <div id="flag" class="dashboard-section">
                    <div class="section-header">
                        <h2>Flag信息</h2>
                        <p>系统中的旗标</p>
                    </div>
                    
                    <div class="flag-container">
                        <div class="flag-item">
                            <h3>管理员Flag</h3>
                            <p><?php echo $first_flag; ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- 个人资料 -->
                <div id="profile" class="dashboard-section">
                    <div class="section-header">
                        <h2>个人资料</h2>
                        <p>查看和编辑个人信息</p>
                    </div>
                    
                    <div class="profile-container">
                        <div class="profile-info">
                            <div class="profile-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            
                            <div class="profile-details">
                                <h3><?php echo htmlspecialchars($username); ?></h3>
                                <p>角色：<?php echo htmlspecialchars($role); ?></p>
                                <p>用户ID：<?php echo $user_id; ?></p>
                                <p>注册时间：2023-01-01</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
    // 模态框功能
    const modal = document.getElementById('passwordModal');
    const changePasswordBtns = document.querySelectorAll('.change-password-btn');
    const closeModal = document.querySelector('.close-modal');
    const userIdInput = document.getElementById('user_id');
    
    // 打开模态框
    changePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            userIdInput.value = userId;
            modal.style.display = 'block';
        });
    });
    
    // 关闭模态框
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    // 点击模态框外部关闭
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // 侧边栏切换
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    });
    
    // 菜单项点击
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            // 只处理页内链接的活动状态
            if (!this.getAttribute('href').includes('.php')) {
                menuItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });
    </script>
</body>
</html> 