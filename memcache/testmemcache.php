<?php
/**
 * 1. 缓存应均匀分别在各节点
 * 2. 添加一个节点, 需要重建的索引数据
 * 3. 一个节点失效后, 需要重建的缓存数量等于此节点中的缓存数量
 * 
 * @todo 如何解决新增节点时原节点的冗余数据
 * @author Farmer.Li <me@farmerli.com>
 */

include_once './MemcacheConnectException.php';
include_once './MemcacheManager.php';
include_once './MemcacheClient.php';
include_once "./MemcacheService.php";

$configs = [
    // 虚拟节点系数, 乘以服务器weight为服务器虚拟节点数
    'virtualNodeCoefficient' => 100,
    // 服务器列表
    'servers' => [
        md5('127.0.0.1:12000') => [
            'host' => '127.0.0.1',
            'port' => 12000,
            'weight' => 1
        ],
        md5('127.0.0.1:12010') => [
            'host' => '127.0.0.1',
            'port' => 12010,
            'weight' => 1
        ],
    ]
];

$m = MemcacheService::getInstance();
// 初始化节点
$m->setVirtualNodeCoefficient(
    $configs['virtualNodeCoefficient']
);
$stat = [];
foreach ($configs['servers'] as $key => $config) {
    $m->addRealServer($config['host'], $config['port'], $config['weight']);
    $stat[$key] = 0;
}

// 清空数据
$m->flush();

// 初始化数据
$data = [];
for ($i = 0; $i < 10000; $i ++) {
    $key = rand();
    while (isset($data[$key])) {
        $key = rand();
    }
    $data[$key] = $i;
}
$count = count($data);
echo "<p>初始化数据：{$count}</p>";

foreach ($data as $key => $value) {
    $m->set($key, $value);
}

printStat($data, $configs['servers'], $m);

/**
 * 添加一个节点
 */
$server3 = [
    'host' => '127.0.0.1',
    'port' => 12020,
    'weight' => 1
];
$m->addRealServer($server3['host'], $server3['port'], $server3['weight']);
$m->flush($server3);
$tmpKey = md5($server3['host'] . ':' . $server3['port']);
$configs['servers'][$tmpKey] = $server3;

$invalidData = rebuildCache($data, $m);

echo "<p>新增服务器：host{$server3['host']}:{$server3['port']}</p>";
echo "<p>未命中数量：{$invalidData}</p>";

printStat($data, $configs['servers'], $m);

/**
 * 使一个节点失效
 */
$server = array_shift($configs['servers']);
$m->setServerInvalid($server);

$invalidData = rebuildCache($data, $m);


echo "<p>删除服务器：host{$server['host']}:{$server['port']}</p>";
echo "<p>未命中数量：{$invalidData}</p>";

printStat($data, $configs['servers'], $m);


function rebuildCache($data, $m)
{
    $invalidData = 0;
    foreach ($data as $key => $value) {
        $v = $m->get($key);
        if (false === $v || $v !== $value) {
            $invalidData ++;
            $m->set($key, $value);
        }
    }
    return $invalidData;
}

function printStat($data, $servers, $m)
{
    $invalidData = 0;
    $stat = [];
    foreach ($data as $k => $v) {
        $value = $m->get($k);
        if ($value === false || $value !== $v) {
            $invalidData ++;
        } else {
            $s = $m->getServerConfigByKey($k);
            $statKey = md5($s['host'] . ':' . $s['port']);
            if (false === isset($stat[$statKey])) {
                $stat[$statKey] = 0;
            }
            $stat[$statKey] ++;
        }
    }
    echo "<h3>统计</h3>";
    $count = array_sum($stat);
    echo "<p>未命中: {$invalidData}, 命中：{$count}</p>";
    foreach ($servers as $k => $s) {
        $t = $stat[$k];
        echo "<p>host: {$s['host']}, port: {$s['port']}, total: {$t}</p>";
    }
}