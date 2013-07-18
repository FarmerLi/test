<?php
$m = new Memcache();
$m->connect(
    '127.0.0.1', 11211
);
$m->set('test', 1);
var_dump($m->get('test'));