#!/bin/bash

# 重置 flag 脚本
# 使用 UUID 生成三个不同的 flag，并写入指定位置

# 检查是否为 root 用户运行
if [ "$(id -u)" -ne 0 ]; then
    echo "错误: 此脚本需要 root 权限运行"
    echo "请使用 sudo 或以 root 用户身份运行"
    exit 1
fi

# 生成三个不同的 UUID
uuid1=$(uuid)
sleep 0.5
uuid2=$(uuid)
sleep 0.5
uuid3=$(uuid)

# 创建 flag 格式
flag1="flag{$uuid1}"
flag2="flag{$uuid2}"
flag3="flag{$uuid3}"

# 写入到指定文件
echo "$flag1" > /root/root.txt
echo "$flag2" > /home/welcome/user.txt
echo "$flag3" > /opt/flag.txt

# 设置适当的权限
chmod 600 /root/root.txt
chown root:root /root/root.txt

chmod 640 /home/welcome/user.txt
chown welcome:welcome /home/welcome/user.txt

chmod 640 /opt/flag.txt
chown www-data:www-data /opt/flag.txt

echo "Flag 重置完成!"
echo "[+] web flag: /opt/flag.txt:"
echo "- $flag3\n"
echo "[+] user flag: /home/welcome/user.txt"
echo "- $flag2\n"
echo "[+] root flag: /root/root.txt"
echo "- $flag1"

