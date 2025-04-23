


          
# CTF 网站安全挑战

这是一个包含多种漏洞的CTF网站安全挑战，参与者需要利用各种技术获取三个flag。

## 环境要求

- PHP 7.0 或更高版本
- MySQL 5.6 或更高版本
- Web服务器 (Apache 推荐)

## 文件结构

```
/
├── index.php           # 网站首页
├── login.php           # 登录页面（含SQL注入漏洞）
├── dashboard.php       # 控制面板（含文件上传和第一个flag）
├── logout.php          # 登出功能
├── config.php          # 数据库配置文件
├── .htaccess           # Apache配置文件（允许执行PHP）
├── database.sql        # 数据库结构和初始数据
├── css/
│   └── style.css       # 网站样式表
├── js/
│   ├── script.js       # 主页和登录页特效
│   └── dashboard.js    # 控制面板交互功能
└── uploads/            # 文件上传目录
```

## 配置文件说明

### 1. 数据库配置文件 (config.php)

位置：网站根目录下的 `config.php`

修改方法：
```php
<?php
// 数据库连接配置
$db_host = 'localhost';    // 数据库服务器地址
$db_user = 'dmsyctfuser';         // 数据库用户名（修改为你的数据库用户）
$db_pass = 'dmsyctfpassword'; // 数据库密码（修改为你的数据库密码）
$db_name = 'coolsite';     // 数据库名称
```

### 2. .htaccess 文件

位置：网站根目录下的 `.htaccess`

这个文件配置了Apache允许将其他文件类型作为PHP执行：

```apache
# 允许 PHP 代码在其他文件类型中执行
AddType application/x-httpd-php .php .phtml .php3 .php4 .php5 .phps .pht .phar .inc

# 无需验证文件上传类型
php_flag file_uploads on
```

### 3. Apache 服务器配置

如果使用Apache，需要在Apache配置文件中启用 .htaccess：

- 对于Ubuntu/Debian：`/etc/apache2/apache2.conf`
- 对于CentOS/RHEL：`/etc/httpd/conf/httpd.conf`

找到对应网站目录的配置，修改为：

```apache
<Directory /var/www/html> # 或你的网站目录
    Options Indexes FollowSymLinks
    AllowOverride All  # 这行很重要
    Require all granted
</Directory>
```

修改后重启Apache:
```bash
sudo systemctl restart apache2.service   # Ubuntu/Debian
# 或
sudo systemctl restart httpd.service     # CentOS/RHEL
```

## 安装步骤

### 1. 准备环境

确保您的系统已安装以下软件：
- PHP 7.0 或更高版本
- MySQL 5.6 或更高版本
- Apache Web 服务器（推荐）

### 2. 下载项目文件

将项目文件克隆或下载到您的本地目录：

```bash
git clone https://github.com/your-username/dmsy-vuln-activities.git
cd dmsy-vuln-activities
```

### 3. 配置 Web 服务器

**Apache 配置:**

确保在 Apache 配置中启用了以下模块：
```bash
sudo a2enmod rewrite
sudo a2enmod php8.2
```

修改 Apache 配置文件，启用 .htaccess：
- Ubuntu/Debian: 编辑 `/etc/apache2/apache2.conf`
- CentOS/RHEL: 编辑 `/etc/httpd/conf/httpd.conf`

找到对应网站目录的配置，修改为：
```apache
<Directory /var/www/html> # 或您的网站目录
    Options Indexes FollowSymLinks
    AllowOverride All  # 这行很重要
    Require all granted
</Directory>
```

重启 Apache 服务：
```bash
sudo systemctl restart apache2.service   # Ubuntu/Debian
# 或
sudo systemctl restart httpd.service     # CentOS/RHEL
```

### 4. 配置数据库

启动 MySQL 服务：
```bash
sudo systemctl start mysql.service
# 或
sudo systemctl start mariadb.service
```

使用 root 用户登录 MySQL：
```bash
mysql -u root -p
```

执行以下 SQL 命令创建数据库和用户：
```sql
CREATE DATABASE coolsite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dmsyctfuser'@'localhost' IDENTIFIED BY 'dmsyctfpassword';
GRANT ALL PRIVILEGES ON coolsite.* TO 'dmsyctfuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

导入数据库结构和初始数据：
```bash
mysql -u dmsyctfuser -p coolsite < database.sql
```

### 5. 配置网站文件

1. 将所有文件复制到 Web 服务器的根目录：

```bash
# Ubuntu/Debian
sudo cp -r * /var/www/html/
sudo cp .htaccess /var/www/html/
```

2. 确保 Web 服务器对上传目录有写入权限：

```bash
# Linux
sudo mkdir -p /var/www/html/uploads
sudo chmod 777 /var/www/html/uploads
```

3. 修改 `config.php` 文件中的数据库连接信息（如有需要）：

```php
<?php
// 数据库连接配置
$db_host = 'localhost';    // 数据库服务器地址
$db_user = 'dmsyctfuser';  // 数据库用户名
$db_pass = 'dmsyctfpassword'; // 数据库密码
$db_name = 'coolsite';     // 数据库名称
```

### 6. 启动服务

如果您使用的是 Apache，确保 Apache 和 MySQL 服务都已启动：

```bash
sudo systemctl start apache2.service   # Ubuntu/Debian
sudo systemctl start mysql.service
# 或
sudo systemctl start httpd.service     # CentOS/RHEL
sudo systemctl start mariadb.service
```

或者，您也可以使用 PHP 内置服务器进行快速测试（不推荐用于生产环境）：

```bash
cd /path/to/website/
php -S 0.0.0.0:8080
```

### 7. 访问网站

在浏览器中访问：
- http://localhost/ （如果使用 Apache）
- http://localhost:8080/ （如果使用 PHP 内置服务器）
- http://服务器IP地址/ （如果从远程访问）

## 默认用户

| 用户名 | 密码 | 角色 |
|-------|------|-----|
| admin | _iloveyou_welcome$ | 管理员 |
| user1 | password | 普通用户 |
| zhangsan | qwerty | 普通用户 |
| test | test | 普通用户 |

## 攻击提示

1. SQL注入：
   - 尝试在登录页面使用 `' OR '1'='1` 作为用户名和密码
   - 或者使用 `admin' --` 作为用户名，任意密码

2. 文件上传漏洞：
   - 尝试上传带有 PHP Webshell

3. 密码复用:
   - user:welcome 的 password 也是 dmsyctfpassword，可以实现 su 登录

4. /home/welcome 权限问题:
   - 可以读取 /home/welcome/bash_history 来获取密码

5. /etc/passwd welcome 可写:
   - 可以添加一个恶意用户，然后用 su 登录

6. sudo 滥用:
   - www-data 可以使用 sudo -l 查看用户的 sudo 权限, 有 sudo -u welcome vi 权限
   - welcome 可以使用 sudo /home/*/backup.sh，删除文件然后新建一个即可

## 注意事项

本CTF挑战仅用于教育目的，请在合法授权的环境中使用。请勿在生产环境部署此代码，也不要将其用于非法活动。

