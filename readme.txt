ssh root@119.29.240.141
mp2Jg#o7Ehy
12345
数据库
mysql -u root -p -S /coldata1/data/mysql/mysql.sock
mp2Jg#o7Ehy

重启服务器后启动服务指令
//重启前杀掉占用端口进程
sudo killall -9 httpd
sudo killall -9 nginx
sudo killall -9 php-fpm

nginx:
sudo /opt/nginx/sbin/nginx

apache:
sudo /opt/apacheCMS/bin/apachectl restart
sudo /opt/apache/bin/apachectl restart

mysql:
sudo service mysqld restart

memcached:
sudo /opt/memcached/bin/memcached -d -u root
