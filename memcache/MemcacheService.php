<?php
include_once './MemcacheConnectException.php';
include_once './MemcacheManage.php';
include_once './MemcacheClient.php';

/**
 * Memcache 一致性哈希实现
 *
 * @author Farmer.Li <me@farmerli.com>
 */
class MemcacheService
{
    private $_serverConfigMap = [];

    private $_config = [];

    private $_virtualNodeCoefficient = null;

    /**
     * 构造, 初始化服务器
     *
     * @return void
     */
    private function __construct()
    {
        $this->_config = $this->getConfig();

        $this->_virtualNodeCoefficient = isset($this->_config['virtualNodeCoefficient']) ?
            max(intval($this->_config['virtualNodeCoefficient']), 1) : 1;

        foreach ($this->_config['servers'] as $server) {
            // $this->addServer($server['host'], $server['port']);
            $nodes = $this->_virtualNodeCoefficient * $server['weight'];
            for ($i = 0; $i <= $nodes; $i ++) {
                $this->addServer($server['host'], $server['port'], $i);
            }
        }

        ksort($this->_serverConfigMap);
    }

    /**
     * 单例
     * 
     * @return self
     */
    static public function getInstance()
    {
        static $m = null;

        if (null === $m) {
            $m = new self;
        }
        return $m;
    }

    /**
     * 添加一个服务器
     * 
     * @param string $host               服务器
     * @param int    $port               端口
     * @param int    $virtualNoteCounter 虚拟节点计数器
     *
     * @return self
     */
    public function addServer($host, $port, $virtualNoteCounter = 0)
    {
        $key = $this->genServerHashKey(
            $host, $port, $virtualNoteCounter
        );
        $this->_serverConfigMap[$key] = [
            'host' => $host,
            'port' => $port
        ];
    }

    /**
     * 生成一个hash key
     * 
     * @param string $key key
     * 
     * @return int
     */
    public function genHashKey($key)
    {
        return sprintf('%u', crc32($key));
    }

    public function genServerHashKey($host, $port, $virtualNoteCounter = 0)
    {
        return $this->genHashKey(
            crc32(
                sprintf(
                    '%s:%s-%s', $host, $port, $virtualNoteCounter
                )
            )
        );
    }

    /**
     * 获取配置信息, 可以通过配置文件实现
     * 
     * @return Array
     */
    public function getConfig()
    {
        return [
            // 虚拟节点系数, 乘以服务器weight为服务器虚拟节点数
            'virtualNodeCoefficient' => 10,
            // 服务器列表
            'servers' => [
                'server1' => [
                    'host' => '127.0.0.1',
                    'port' => 12000,
                    'weight' => 1
                ]
            ]
        ];
    }

    public function set($key, $value)
    {
        $server = $this->getServer($key);
        //return $server->set($key, $value);
    }

    public function get($key)
    {
        return $this->getServer($key)->get($key);
    }

    /**
     * 获取服务器
     * 
     * @param string $key key
     * 
     * @return MemcacheClient
     */
    public function getServer($key)
    {
        $key = $this->genHashKey($key);
        $config = null;
        $client = null;
        while (null === $client) {
            $client = MemecacheManager::getInstance()->get($config);
            if (null === $client) {
                $this->_setServerInvalid($config);
                $config = $this->_findConfig($key)
            }
            if (null === $config) {
                throw new MemcacheConnectException();
            }
        }
        return $client;
    }

    /**
     * 设置服务器失效, 将此配置的服务器和虚拟节点从环上去除, 并报警
     * 
     * @param Array $config 服务器配置
     *
     * @return void
     */
    public function setServerInvalid($config)
    {
        $fullConfig = [];
        foreach ($this->_config['servers'] as $key => $row) {
            if ($config['host'] == $row['host'] && $config['port'] == $row['port']) {
                $fullConfig = $row;
                break;
            }
        }
        if (empty($fullConfig)) {
            return;
        }
        $nodes = $this->_virtualNodeCoefficient * $fullConfig['weight'];
        for ($i = 0; $i <= $nodes; $i ++) {
            $key = $this->genServerHashKey(
                $fullConfig['host'], $fullConfig['port'], $i
            );
            unset($this->_serverConfigMap[$key]);
        }

        MemcacheManager::removeServer($fullConfig);
    }

    /**
     * 查找服务器映射节点配置
     * 
     * @param int $key key
     * 
     * @return Array
     */
    private function _findConfig($key)
    {
        $keys = array_keys($this->_serverConfigMap);
        $count = count($keys);
        if ($key <= $keys[0] || $key > $keys[$count - 1]) {
            return $this->_serverConfigMap[$keys[0]];
        }
        $config = null;
        //二分查找向前取最近的节点
        $minIndex = 0;
        $maxIndex = $count - 1;
        $i = 0;
        while (null === $config) {
            $middleKey = intval($minIndex + ceil(($maxIndex - $minIndex) / 2));
            if ($key > $keys[$middleKey]) {
                $minIndex = $middleKey;
            } else {
                $maxIndex = $middleKey;
            }
            if ($maxIndex - $minIndex <= 1) {
                $config = $this->_serverConfigMap[$keys[$maxIndex]];
            }
        }
        return $config;
    }

}