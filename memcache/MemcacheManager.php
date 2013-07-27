<?php
class MemcacheManager
{
    private $_servers = [];

    private function __construct()
    {

    }

    static public function getInstance()
    {
        static $mm = null;
        if (null === $mm) {
            $mm = new self;
        }

        return $mm;
    }

    /**
     * get a server by config
     * 
     * @param Array   $config server config
     * @param boolean $create 如果不存在, 是否创建连接
     * 
     * @return MemcacheClient
     */
    public function get($config, $create = true)
    {
        $key = $this->_genKey($config['host'] . $config['port']);
        if (!isset($this->_servers[$key]) && true === $create) {
            $this->_createServer($key, $config);
        }

        return $this->_servers[$key];
    }

    private function _createServer($key, $config)
    {
        $client = new MemcacheClient($config);
        $this->_servers[$key] = $client;
        return $client;
    }

    public function removeServer($config)
    {
        $key = $this->_genKey($config['host'] . $config['port']);
        if ($this->_servers[$key]) {
            unset($this->_servers[$key]);
        }
    }

    private function _genKey($str)
    {
        return sprintf(
            '%u',
            crc32($str)
        );
    }
}