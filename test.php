<?php
$content = [
	"http://baidu.com/index.php",
	"http://baidu.com/index.php?a=b",
	"http://baidu.com/index.php?a=b#id=1",
	"http://www.baidu.com/index.php",
	"http://baidu.com/index.php?file=xx.html",
	"http://baidu.com/index.php?file=xx.html#id=fileext.htm"
];
$regx = '/\.(\w+)?[^\/|\?|#]?/';
foreach ($content as $row) {
	preg_match($regx, $row, $a);
	var_dump($a);
}