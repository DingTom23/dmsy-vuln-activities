# 结束总结


### 1. 协议理解和包理解(网络通信基础)
```

首先提出一些问题
就是这个 网络通信基础 和 Linux 基础 太差了
不能够了解包的结构，在讲之前先讲一下 web 包的结构

GET / HTTP/1.1
Host: 192.168.31.170
Accept-Language: zh-CN,zh;q=0.9
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Accept-Encoding: gzip, deflate, br
Connection: keep-alive

这是一个 GET method 的包

GET /games.php?game=ball.php HTTP/1.1
Host: 192.168.31.170
Accept-Language: zh-CN,zh;q=0.9
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Referer: http://192.168.31.170/games.php
Accept-Encoding: gzip, deflate, br
Cookie: PHPSESSID=cra00460o420p6lba717498q84
Connection: keep-alive

这也是一个 GET method 的包 
你可以看到，参数是在后面的 ?game=ball.php

POST /login.php HTTP/1.1
Host: 192.168.31.170
Content-Length: 42
Cache-Control: max-age=0
Accept-Language: zh-CN,zh;q=0.9
Origin: http://192.168.31.170
Content-Type: application/x-www-form-urlencoded
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Referer: http://192.168.31.170/login.php
Accept-Encoding: gzip, deflate, br
Cookie: PHPSESSID=cra00460o420p6lba717498q84
Connection: keep-alive

username=admin&password=babygirl94andrew10

这是一个 POST 的
你可以看到，POST 在下面包的内容中 username=admin&password=babygirl94andrew10

GET 方法请求一个指定资源的表示形式，使用 GET 的请求应该只被用于获取数据。
HEAD 方法请求一个与 GET 请求的响应相同的响应，但没有响应体。
POST 方法用于将实体提交到指定的资源，通常导致在服务器上的状态变化或副作用。
PUT 方法用有效载荷请求替换目标资源的所有当前表示。
DELETE 方法删除指定的资源。
CONNECT 方法建立一个到由目标资源标识的服务器的隧道。
OPTIONS 方法用于描述目标资源的通信选项。
TRACE 方法沿着到目标资源的路径执行一个消息环回测试。
PATCH 方法用于对资源应用部分修改。

这就是一些基本的一些方法
如果设置的 OPTIONS 方法的相关设置 用 options 就能得到可以用什么协议去访问

### 2. Linux 基础
