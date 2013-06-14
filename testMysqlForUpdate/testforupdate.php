<?php
$start = time() + microtime();
$lk = mysql_connect('localhost', 'root', 'theadmin');
mysql_select_db('sap_prod');
mysql_query('set names utf8');
$i = 1000;
while ($i) {
    mysql_query('begin');
    $query = mysql_query('select * from stock_detail where id = 1 for update');
    $row = mysql_fetch_array($query);
    $query = mysql_query('select * from products where name like "%手机%" limit 100');
    $query = mysql_query('select * from products where sku like "%110000%" limit 100');
    if ($row['storage_id'] > 90) {
        mysql_query("update stock_detail set storage_id = storage_id - 1 where id = {$row['id']}");
    }
    
    mysql_query('commit');
    $i--;
}
echo time() + microtime() - $start;

/**
 * 6.8156378269196 for update
 * 3.3277859687805 for update
 * 3.6010150909424 for update
 * 3.6010150909424 for update
 * 4.3148138523102 for update
 * 3.7802481651306 for update
 * 
 * 4.4087018966675
 * 5.4502007961273
 * 5.2499830722809
 * 3.9698600769043
 * 5.9321761131287
 * 4.1303360462189
 * 3.3233530521393
 *
 * 3.3877940177917 3.3800711631775 for update
 * 3.1434321403503 3.184583902359 for update
 * 2.8325488567352 2.8114280700684 for update
 * 3.4325380325317 3.3745260238647 for update
 */