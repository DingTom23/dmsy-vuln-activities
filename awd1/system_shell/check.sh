#!/bin/bash

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
NC='\033[0m' # 无颜色

echo -e "${YELLOW}[*] 开始检查 /var/www/html 下的文件...${NC}"
echo "=================================================="

# 初始化计数器
total_files=0
missing_files=0
empty_files=0

# 检查文件是否存在且大小不为0
check_file() {
    local file_path="/var/www/html/$1"
    total_files=$((total_files + 1))
    
    if [ ! -f "$file_path" ]; then
        echo -e "${RED}[-] $file_path 不存在!${NC}"
        missing_files=$((missing_files + 1))
    else
        if [ ! -s "$file_path" ]; then
            echo -e "${YELLOW}[!] $file_path 存在但大小为0!${NC}"
            empty_files=$((empty_files + 1))
        else
            echo -e "${GREEN}[+] $file_path 存在且大小不为0${NC}"
        fi
    fi
}

# 检查目录是否存在
check_directory() {
    local dir_path="/var/www/html/$1"
    
    if [ ! -d "$dir_path" ]; then
        echo -e "${RED}[-] 目录 $dir_path 不存在!${NC}"
    else
        echo -e "${GREEN}[+] 目录 $dir_path 存在${NC}"
    fi
}

# 检查主要PHP文件
echo -e "${YELLOW}[*] 检查PHP文件...${NC}"
check_file "index.php"
check_file "login.php"
check_file "dashboard.php"
check_file "games.php"
check_file "ball.php"
check_file "snake.php"
check_file "puzzle.php"
check_file "memory.php"
check_file "typing.php"
check_file "config.php"
echo ""

# 检查CSS和JS目录
echo -e "${YELLOW}[*] 检查目录...${NC}"
check_directory "css"
check_directory "js"
check_directory "uploads"
echo ""

# 检查CSS文件
echo -e "${YELLOW}[*] 检查CSS文件...${NC}"
check_file "css/style.css"
echo ""

# 检查JS文件
echo -e "${YELLOW}[*] 检查JavaScript文件...${NC}"
check_file "js/script.js"
check_file "js/dashboard.js"
echo ""

# 检查配置文件
echo -e "${YELLOW}[*] 检查配置文件...${NC}"
check_file ".htaccess"
echo ""

# 输出统计结果
echo "=================================================="
echo -e "${YELLOW}[*] 检查结果统计:${NC}"
echo -e "总共检查文件数: $total_files"
echo -e "缺失文件数: $missing_files"
echo -e "空文件数: $empty_files"
echo -e "正常文件数: $((total_files - missing_files - empty_files))"

if [ $missing_files -eq 0 ] && [ $empty_files -eq 0 ]; then
    echo -e "${GREEN}[+] 恭喜！所有文件都存在且大小不为0${NC}"
else
    echo -e "${RED}[-] 警告：有 $missing_files 个文件缺失，$empty_files 个文件为空${NC}"
fi
echo ""
echo -e "${YELLOW}[*] 开始检测扣分情况...${NC}"
echo "=================================================="

# 初始化扣分计数器
total_deduction=0

# 检查flag文件是否存在
check_flag_files() {
    echo -e "${YELLOW}[*] 检查flag文件...${NC}"
    
    # 检查/opt/flag.txt
    if [ ! -f "/opt/flag.txt" ]; then
        echo -e "${RED}[-] /opt/flag.txt 不存在! (-500分)${NC}"
        total_deduction=$((total_deduction + 500))
    else
        echo -e "${GREEN}[+] /opt/flag.txt 存在${NC}"
    fi
    
    # 检查/root/root.txt
    if [ ! -f "/root/root.txt" ]; then
        echo -e "${RED}[-] /root/root.txt 不存在! (-500分)${NC}"
        total_deduction=$((total_deduction + 500))
    else
        echo -e "${GREEN}[+] /root/root.txt 存在${NC}"
    fi
    
    # 检查所有用户目录中的user.txt
    for user_dir in /home/*; do
        if [ -d "$user_dir" ]; then
            username=$(basename "$user_dir")
            if [ ! -f "$user_dir/user.txt" ]; then
                echo -e "${RED}[-] $user_dir/user.txt 不存在! (-500分)${NC}"
                total_deduction=$((total_deduction + 500))
            else
                echo -e "${GREEN}[+] $user_dir/user.txt 存在${NC}"
            fi
        fi
    done
}

# 检查是否存在反弹shell
check_reverse_shell() {
    echo -e "${YELLOW}[*] 检查反弹shell...${NC}"
    
    # 检查常见的反弹shell特征
    reverse_shell_count=0
    
    # 检查可疑的网络连接
    suspicious_connections=$(netstat -antup 2>/dev/null | grep -E '(ESTABLISHED|SYN_SENT)' | grep -v '127.0.0.1' | grep -v '::1')
    
    if [ -n "$suspicious_connections" ]; then
        echo -e "${RED}[-] 发现可疑的网络连接:${NC}"
        echo "$suspicious_connections"
        
        # 检查这些连接是否是反弹shell
        for pid in $(echo "$suspicious_connections" | awk '{print $7}' | cut -d'/' -f1 | sort -u); do
            if [ -n "$pid" ] && [ "$pid" != "-" ]; then
                cmd=$(ps -p "$pid" -o cmd= 2>/dev/null)
                # 排除 systemd-timesyncd 进程
                if echo "$cmd" | grep -qE '(bash|sh|nc|python|perl|php|ruby|telnet|socat|busybox)' && ! echo "$cmd" | grep -q "/lib/systemd/systemd-timesyncd"; then
                    echo -e "${RED}[-] 进程 $pid 可能是反弹shell: $cmd (-100分)${NC}"
                    reverse_shell_count=$((reverse_shell_count + 1))
                fi
            fi
        done
    else
        echo -e "${GREEN}[+] 未发现可疑的网络连接${NC}"
    fi
    
    # 检查可疑进程
    suspicious_processes=$(ps aux | grep -E '(nc|ncat|netcat|bash -i|python -c|perl -e|ruby -e|php -r)' | grep -v grep | grep -v "/lib/systemd/systemd-timesyncd")
    
    if [ -n "$suspicious_processes" ]; then
        echo -e "${RED}[-] 发现可疑进程:${NC}"
        echo "$suspicious_processes"
        
        # 计算可疑进程数量
        suspicious_count=$(echo "$suspicious_processes" | wc -l)
        echo -e "${RED}[-] 发现 $suspicious_count 个可疑进程，可能是反弹shell${NC}"
        
        # 如果之前没有通过网络连接发现反弹shell，则计入扣分
        if [ $reverse_shell_count -eq 0 ] && [ $suspicious_count -gt 0 ]; then
            echo -e "${RED}[-] 可能存在反弹shell (-100分)${NC}"
            total_deduction=$((total_deduction + 100))
        fi
    else
        echo -e "${GREEN}[+] 未发现可疑进程${NC}"
    fi
}

# 检查业务服务是否正常运行
check_services() {
    echo -e "${YELLOW}[*] 检查关键业务服务...${NC}"
    
    # 检查Apache服务
    if pgrep apache2 >/dev/null; then
        echo -e "${GREEN}[+] Apache服务正在运行${NC}"
    else
        echo -e "${RED}[-] Apache服务未运行! (-500分)${NC}"
        total_deduction=$((total_deduction + 500))
    fi
    
    # 检查MySQL/MariaDB服务
    if pgrep -f mariadbd >/dev/null || pgrep -f mysqld >/dev/null; then
        echo -e "${GREEN}[+] 数据库服务正在运行${NC}"
    else
        echo -e "${RED}[-] 数据库服务未运行! (-500分)${NC}"
        total_deduction=$((total_deduction + 500))
    fi
    
    # 检查FTP服务
    if pgrep -f vsftpd >/dev/null; then
        echo -e "${GREEN}[+] FTP服务正在运行${NC}"
    else
        echo -e "${RED}[-] FTP服务未运行! (-500分)${NC}"
        total_deduction=$((total_deduction + 500))
    fi
    
    # 检查SSH服务
    if pgrep -f "sshd" >/dev/null; then
        echo -e "${GREEN}[+] SSH服务正在运行${NC}"
    else
        echo -e "${RED}[-] SSH服务未运行! (-500分)${NC}"
        total_deduction=$((total_deduction + 500))
    fi
}

# 执行所有检查
check_flag_files
echo ""
check_reverse_shell
echo ""
check_services
echo ""

# 输出总扣分
echo "=================================================="
if [ $total_deduction -eq 0 ]; then
    echo -e "${GREEN}[+] 恭喜！未发现扣分项${NC}"
else
    echo -e "${RED}[-] 总扣分: $total_deduction 分${NC}"
fi