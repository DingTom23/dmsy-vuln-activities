<?php
session_start();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Academy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="particles-container" id="particles-js"></div>
    
    <div class="hero-container">
        <div class="logo-container">
            <div class="logo">
                <i class="fas fa-code"></i>
                <span>Code Academy</span>
            </div>
            <div class="main-menu">
                <a href="#about" class="menu-link">关于我们</a>
                <a href="#courses" class="menu-link">课程</a>
                <a href="#contact" class="menu-link">联系</a>
                <a href="login.php" class="btn primary-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>登录</span>
                </a>
            </div>
        </div>
        
        <div class="content-wrapper">
            <div class="hero-content">
                <h1>编程学习平台</h1>
                <p class="subtitle">培养未来科技人才</p>
                <div class="menu-buttons">
                    <a href="login.php" class="btn primary-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>登录系统</span>
                    </a>
                    <a href="#about" class="btn secondary-btn">
                        <i class="fas fa-info-circle"></i>
                        <span>了解更多</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <section id="about" class="section">
        <div class="container">
            <h2 class="section-title">关于我们</h2>
            <div class="about-content">
                <p>Code Academy 是一所不知道啥的实验室。</p>
            </div>
        </div>
    </section>
    
    <section id="courses" class="section">
        <div class="container">
            <h2 class="section-title">热门课程</h2>
            <div class="courses-grid">
                <div class="course-card">
                    <div class="course-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3>Web 开发基础</h3>
                    <p>学习 HTML, CSS 和 JavaScript 构建现代化网站</p>
                </div>
                <div class="course-card">
                    <div class="course-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>网络安全入门</h3>
                    <p>了解网络安全基础知识和防御技术</p>
                </div>
                <div class="course-card">
                    <div class="course-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3>数据库设计</h3>
                    <p>掌握数据库结构和SQL语言</p>
                </div>
            </div>
        </div>
    </section>
    
    <section id="contact" class="section">
        <div class="container">
            <h2 class="section-title">联系我们</h2>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>地址：不知道</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <span>电话：不知道</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>邮箱：不知道</span>
                </div>
            </div>
        </div>
    </section>
    
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <i class="fas fa-code"></i>
                    <span>Code Academy</span>
                </div>
                <div class="footer-links">
                    <a href="#about">关于我们</a>
                    <a href="#courses">课程介绍</a>
                    <a href="#contact">联系方式</a>
                </div>
                <div class="social-links">
                    <a href="#"><i class="fab fa-weixin"></i></a>
                    <a href="#"><i class="fab fa-weibo"></i></a>
                    <a href="#"><i class="fab fa-github"></i></a>
                </div>
            </div>
            <div class="copyright">
                &copy; 2025 Code Academy. 保留所有权利.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html> 