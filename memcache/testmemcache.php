<?php
/**
 * 1. 缓存应均匀分别在各节点
 *
 * 2. 一个节点失效后, 需要重建的缓存数量等于此节点中的缓存数量
 * 
 * 3. 添加一个节点
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
// 初始化
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

// 插入数据
$data = [];
for ($i = 0; $i < 10000; $i ++) {
    $key = rand();
    $data[$key] = $i;
    $m->set($key, $i);
    $tmp = $m->getServerConfigByKey($key);
    $stat[md5($tmp['host'] . ':' . $tmp['port'])] ++;
}

// 打印数据分布情况
foreach ($stat as $key => $count) {
    $server = $configs['servers'][$key];
    echo <<<eof
    <p><b>host: {$server['host']}:{$server['port']}<b> -- total: {$count}</p>
eof;
}

/**
 * 添加一个服务器
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
$stat[$tmpKey] = 0;

$invalidTotal = 0;
foreach ($data as $key => $value) {
    $value = $m->get($key);
    if (false === $value) {
        $m->set($key, $value);
        $invalidTotal ++;
        $stat[$tmpKey] ++;
    }
}

echo "<br /><p><b>host{$server3['host']}:{$server3['port']}</b> -- total: $stat[$tmpKey]</p>";