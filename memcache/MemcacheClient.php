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
        $this->_client->connect(
            $this->_config['host'], $this->_config['port']
        );
        return $this;
    }

    public function __call($func, $params)
    {
        if (!method_exists($this->_client, $func)) {
            throw new Exception ('unknown method: ' . $func);
        }

        return call_user_func_array([$this->_client, $func], $params);
    }
}