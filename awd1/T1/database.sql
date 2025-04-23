-- 创建数据库
CREATE DATABASE IF NOT EXISTS coolsite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 使用数据库
USE coolsite;

-- 创建用户表
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 插入示例用户数据（使用明文弱密码）
INSERT INTO users (username, password, email, role) VALUES 
('admin', 'babygirl94andrew10', 'admin@dmsy.cn', 'admin'),
('user1', '1234567', 'user1@dmsy.cn', 'user'),
('zhangsan', 'qwerty', 'zhangsan@dmsy.cn', 'user'),
('test', 'test', 'test@dmsy.cn', 'user');

-- 创建用户配置表
CREATE TABLE IF NOT EXISTS user_settings (
    user_id INT PRIMARY KEY,
    theme VARCHAR(20) DEFAULT 'light',
    notifications BOOLEAN DEFAULT true,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 插入示例用户配置
INSERT INTO user_settings (user_id, theme, notifications) VALUES 
(1, 'dark', true),
(2, 'light', true),
(3, 'auto', false);

-- 创建上传文件记录表
CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
