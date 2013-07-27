<?php
$lk = mysql_connect('192.168.123.243', 'root', 'mydb123');
mysql_select_db('wms');
mysql_query('set names utf8');
$query = mysql_query('select * from Regions');
while ($row = mysql_fetch_array($query)) {
    $path = '/';
    if ($row['city'] != '') {
        $pid = getProvId($row['province']);
        $path .= $pid . '/';
    }
    if ($row['district']) {
        $cid = getCityId($row['province'], $row['city']);
        $path .= $cid . '/';
    }

    mysql_query(  "update Regions set `path` = '{$path}' where id = {$row['id']};");
}


function getProvId($p) {
    $query = mysql_query("select id from Regions where `province` = '{$p}' and `city` = '' and `district` = ''");
    return mysql_fetch_row($query)[0];
}

function getCityId($p, $c) {
    $query = mysql_query("select id from Regions where `province` = '{$p}' and `city` = '{$c}' and `district` = ''");
    return mysql_fetch_row($query)[0];
}

?>