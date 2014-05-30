<?php
$m = new Memcache();
$m->connect('221.122.126.216', 11211);
$arr=$m->get('ksdata_20131216145505');
var_dump($arr);
//var_dump(json_decode($arr['etimu']));
//$m->delete('ksdata_20131216145505');
