<?php
$client = new SoapClient(null, array('location' => 'http://test.php.com/soapServer.php', 'uri' => 'http://test-uri/'));
$a = $client->testFunc();
var_dump($a);
