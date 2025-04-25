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

// 可用游戏列表
$available_games = [
    'ball.php' => '弹球游戏',
    'snake.php' => '贪吃蛇',
    'puzzle.php' => '拼图游戏',
    'memory.php' => '记忆游戏',
    'typing.php' => '打字游戏'
];

$game_content = '';
$current_game = '';
$game_description = '';

if (isset($_GET['game'])) {
    $current_game = $_GET['game'];
    @include($current_game);
    
    // 设置游戏描述
    if (array_key_exists($current_game, $available_games)) {
        $game_description = $available_games[$current_game];
    } else {
        $game_description = '未知游戏';
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>游戏中心 - Code Academy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .game-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .game-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .game-card {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
        }
        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .game-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #4e73df;
        }
    </style>
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
            
            <div class="dashboard-menu">
                <a href="dashboard.php" class="menu-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>控制台</span>
                </a>
                
                <a href="games.php" class="menu-item active">
                    <i class="fas fa-gamepad"></i>
                    <span>游戏中心</span>
                </a>
                
                <?php if ($role === 'admin'): ?>
                <a href="dashboard.php#upload" class="menu-item">
                    <i class="fas fa-upload"></i>
                    <span>文件上传</span>
                </a>
                
                <a href="dashboard.php#users" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>用户管理</span>
                </a>
                <?php endif; ?>
                
                <a href="dashboard.php#profile" class="menu-item">
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
                    <h1>游戏中心</h1>
                </div>
                
                <div class="header-actions">
                    <a href="dashboard.php" class="btn small-btn">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>返回控制台</span>
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
                
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>可用游戏</h2>
                        <p>选择一个游戏开始玩吧!</p>
                    </div>
                    
                    <div class="game-list">
                        <?php foreach ($available_games as $game_file => $game_name): ?>
                        <a href="games.php?game=<?php echo $game_file; ?>" class="game-card">
                            <div class="game-icon">
                                <i class="fas fa-gamepad"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($game_name); ?></h3>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($current_game): ?>
                    <div class="game-container">
                        <h2><?php echo htmlspecialchars($game_description); ?></h2>
                        <div id="game-area">
                            <!-- 游戏内容将通过include方式加载到这里 -->
                            <?php if (!array_key_exists($current_game, $available_games)): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    游戏加载失败或游戏不存在
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html> 