<?php
include "./MemcacheService.php";

$m = MemcacheService::getInstance();

$m->set('test', '1');