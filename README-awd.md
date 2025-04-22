# AWD 模式攻击

## 凭证信息 (Credentials)

-   **MySQL Root 用户:**
    ```
    mysql:root:$1$0kRGbLi5$o1jTxIVwcDWSOcKtTLukM1
    ```
-   **Linux Root 用户:**
    ```
    linux:root:$1$DcI4hlB5$7Fzb9WR6XrBFwjHgBcpA61
    ```
-   **Linux welcome 用户:**
    ```
    linux:welcome:welcome
    ```

## FTP 服务

-   允许匿名登录。
-   可以访问服务器根目录 (`/`)。
-   允许上传文件。

## Web 目录权限 (`/var/www/html/`)

-   权限设置为 `777` (可读、可写、可执行)。
-   **利用方式:** 结合 FTP 匿名上传权限，可以直接上传 PHP Webshell (例如 `shell.php`) 到 Web 根目录，实现远程代码执行 (RCE)。

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

-   (待补充具体的 sudo 滥用细节)

## TODO

-   (待补充需要完成的事项)
