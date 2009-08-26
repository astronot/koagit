<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_koa = "localhost";
$database_koa = "koait";
$username_koa = "root";
$password_koa = "";
$koa = mysql_pconnect($hostname_koa, $username_koa, $password_koa) or trigger_error(mysql_error(),E_USER_ERROR); 
?>