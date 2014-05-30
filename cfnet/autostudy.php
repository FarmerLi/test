<?php
$uid = array('9698', '10017');
$m = new Memcache();
$m->connect(
    '221.122.126.216', 11211
);
var_dump($m->get('ksdata_20130910102231'));
exit;
/*
while(true) {
    foreach ($uid as $row) {
        if (@$m->get("{$row}_groupstudy") !== false)
            $m->set("{$row}_groupstudy", 0, 60);    
    }
    sleep(1);
}
