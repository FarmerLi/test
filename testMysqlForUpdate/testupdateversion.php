<?php
$start = time() + microtime();
$lk = mysql_connect('localhost', 'root', 'theadmin');
mysql_select_db('sap_prod');
mysql_query('set names utf8');
$i = 1000;
while ($i) {
    mysql_query('begin');
    $query = mysql_query('select * from stock_detail where id = 1');
    $row = mysql_fetch_array($query);
    $query = mysql_query('select * from products where name like "%手机%" limit 100');
    $query = mysql_query('select * from products where sku like "%110000%" limit 100');
    if ($row['storage_id'] > 100) {
        mysql_query("update stock_detail set storage_id = storage_id - 1 where id = {$row['id']} and version");
    }
    
    mysql_query('commit');
    $i--;
}
echo time() + microtime() - $start;
