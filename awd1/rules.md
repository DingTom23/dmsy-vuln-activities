1. 可以进行修复漏洞，不允许删除文件
2. 可以进行 kill 漏洞服务，不允许 kill 自己机器的正常业务服务
3. 不要随便修改自己的 root 密码，也不要修改对面的 root 密码，可以留后门，因为我们要进行 check 和更换 flag，所以会需要进入各队靶机种
4. 20 min 一个周期，更换 flag 和进行 check
5. 记分规则:
```
flag:
/opt/flag.txt -- 100 分
/home/{user}/user.txt -- 300 分
/root/root.txt -- 500 分

check:
有reverse shell: -- -100 分
严重影响业务: -- -500 分
重置机器: -- -1000 分
```