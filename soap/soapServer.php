<?php
function testFunc()
{
	return array('he!');
}
$soap = new SoapServer(null, array('uri' => 'http://test-uri/'));
$soap->addFunction('testFunc');
$soap->handle();
