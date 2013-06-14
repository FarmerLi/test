<?php

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
        $config = $this->getConfig();

        $this->setVirtualNodeCoefficient(
            isset($config['virtualNodeCoefficient']) ?
                $config['virtualNodeCoefficient'] : 10
        );

        if (isset($config['servers'])) {
            foreach ($config['servers'] as $server) {
                $this->addRealServer(
                    $server['host'], $server['port'], $weight
                );
            }
        }
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
     * 添加一个真实服务器
     * 
     * @param string $host   host
     * @param int    $port   端口
     * @param int    $weight 权重
     *
     * @return void
     */
    public function addRealServer($host, $port, $weight = 1)
    {
        $key = md5($host . ':' . $port);
        if (isset($this->_config['servers'][$key])) {
            throw new Exception('server is exists');
        }
        $nodes = $this->_virtualNodeCoefficient * $weight;
        for ($i = 0; $i < $nodes; $i ++) {
            $this->_addVirtualNode($host, $port, $i);
        }
        ksort($this->_serverConfigMap);
        $this->_config['servers'][$key] = [
            'host' => $host,
            'port' => $port,
            'weight' => $weight
        ];
    }

    /**
     * 设置虚拟节点系数
     * 
     * @param int $c 系数
     *
     * @return void
     */
    public function setVirtualNodeCoefficient($c)
    {
        $this->_virtualNodeCoefficient = max(intval($c), 1);
        $this->_config['virtualNodeCoefficient'] = $c;
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
    public function _addVirtualNode($host, $port, $virtualNoteCounter = 0)
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
        // return [
        //     // 虚拟节点系数, 乘以服务器weight为服务器虚拟节点数
        //     'virtualNodeCoefficient' => 10,
        //     // 服务器列表
        //     'servers' => [
        //         [
        //             'host' => '127.0.0.1',
        //             'port' => 12000,
        //             'weight' => 1
        //         ],
        //         [
        //             'host' => '127.0.0.1',
        //             'port' => 12010,
        //             'weight' => 1
        //         ],
        //     ]
        // ];
        return [];
    }

    public function set($key, $value)
    {
        $server = $this->getServer($key);
        return $server->set($key, $value);
    }

    public function get($key)
    {
        return $this->getServer($key)->get($key);
    }

    public function flush()
    {
        foreach ($this->_config['servers'] as $key => $config) {
            $client = $this->memcacheManager()->get($config)->flush();
        }
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
            $config = $this->_findConfig($key);
            $client = $this->memcacheManager()->get($config);
            if (null === $client) {
                $this->_setServerInvalid($config);
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
        // @todo 标记配置文件, 使该配置的服务器不可用

        $this->memcacheManager()->removeServer($fullConfig);
    }

    public function memcacheManager()
    {
        return MemcacheManager::getInstance();
    }

    /**
     * 查找key映射节点的配置
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

    public function getServerConfigByKey($key)
    {
        return $this->_findConfig($key);
    }

}