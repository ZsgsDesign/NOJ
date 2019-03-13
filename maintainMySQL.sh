#!/bin/sh
pidof mysqld >/dev/null
if [ $? -eq 0 ]
then
echo "It is running."
else
echo "At `date` MySQL Server was stopped">> /home/mysql_log
/etc/init.d/mysql start
fi
