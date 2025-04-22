cred:
mysql:root:$1$0kRGbLi5$o1jTxIVwcDWSOcKtTLukM1
linux:root:$1$DcI4hlB5$7Fzb9WR6XrBFwjHgBcpA61
linux:welcome:welcome


ftp 服务:
允许匿名登录
可以全目录跑
可以上传文件

/var/www/html/ 权限777:
配合 ftp 可上传 shell.php 实现 RCE

welcome 用户密码复用:
welcome:welcome
tips: 
/home/welcome/note.txt:
From root@localhost:

        朋友，你的密码太弱了，一旦知道你的用户名，不久知道密码了吗？

                        To welcome@localhost

sudo 滥用:
