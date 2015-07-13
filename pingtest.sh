#!/bin/bash
filename=/root/shams/op1.txt
touch $filename
while [ 1 ]
do
        date >> $filename
        echo "==============================" >> $filename
        ping -c 5 10.133.40.10  >> $filename
done
