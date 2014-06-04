<?php
/**
 * request接口
 *
 * @author Farmer.Li <me@farmerli.com>
 */
namespace Resttest\Request;

/**
 * request 接口
 */
interface RequestInterface
{
    /**
     * 设置请求目标
     * 
     * @param string $url url
     *
     * @return self
     */
    public function setTarget($url);

    /**
     * 设置请求类型
     * 
     * @param string $type POST/GET/PUT/DELETE
     *
     * return self
     */
    public function setMethod($type = 'POST');

    /**
     * 添加一个请求参数
     * 
     * @param string $key   键
     * @param string $value 值
     *
     * @return self
     */
    public function addParam($key, $value, $isupload = false);

    /**
     * 删除一个参数
     * 
     * @param string $key 键
     * 
     * @return void
     */
    public function removeParam($key);

    /**
     * 发出请求
     * 
     * @return Response
     */
    public function send();
}