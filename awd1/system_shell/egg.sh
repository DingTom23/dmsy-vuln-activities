#!/bin/bash

echo "恭喜你找到彩蛋!!!" | nc -lvp 6666 -s 127.0.0.1 -e /bin/bash