<?php
class MemcacheClient
{
    private $_client = null;

    private $_config = [];

    public function __construct($config)
    {
        $this->_client = new Memcache();
        $this->_config = $config;
        $this->_connect();
    }

    public function _connect()
    {

    }
}