<?php
$m = new Memcache();
$m->connect(
    '221.122.126.216', 11211
);
var_dump($m->get('test_xxx'));
