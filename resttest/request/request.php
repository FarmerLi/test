<?php
/**
 * request util
 *
 * @author Farmer.Li <me@farmerli.com>
 */
namespace Resttest\Request;

use Resttest\Request\RequestInterface,
    Resttest\Response\Response,
    Resttest\RestClient;

class Request implements RequestInterface
{
    private $_target = '';

    private $_params = [];

    private $_method = '';

    private $_response;

    protected $restClient;

    /**
     * 构造
     *
     * @return void
     */
    public function __construct()
    {
        $this->restClient = RestClient::getInstance();
    }

    /**
     * 设置请求目标
     * 
     * @param string $url url
     *
     * @return self
     */
    public function setTarget($url)
    {
        $this->_target = $url;
        return $this;
    }

    /**
     * 设置请求类型
     * 
     * @param string $type POST/GET/PUT/DELETE
     *
     * return self
     */
    public function setMethod($type = 'POST')
    {
        $type = strtoupper($type);
        if (!in_array($type, ['POST', 'GET', 'PUT', 'DELETE'])) {
            throw new Exception ('unknown request type: ' . $type);
        }
        $this->_method = $type;
        return $this;
    }

    /**
     * 添加一个请求参数
     * 
     * @param string  $key      键
     * @param string  $value    值
     * @param boolean $isupload 是否上传
     *
     * @return self
     */
    public function addParam($key, $value, $isupload = false)
    {
        $this->_params[$key] = [
            'value' => $value,
            'isupload' => $isupload === true ? true : false
        ];
        return $this;
    }

    /**
     * 删除一个参数
     * 
     * @param string $key 键
     * 
     * @return void
     */
    public function removeParam($key)
    {
        if (isset($this->_params[$key])) {
            unset($this->_params[$key]);
        }
        return $this;
    }

    /**
     * 发出请求
     *
     * @param $string $responseType response type
     * 
     * @return Response
     */
    public function send($responseType = '')
    {
        if (!$this->_method) {
            $this->_method = 'POST';
        }
        if (!$this->_target) {
            throw new Exception ('not set request target');
        }
        
        $this->restClient
            ->setUrl($this->_target)
            ->setMethod($this->_method);

        foreach ($this->_params as $key => $row) {
            if ($row['isupload'] === false) {
                $this->restClient->addParam($key, $row['value']);
            } else {
                if ($this->_method != 'POST') {
                    throw new \Exception('upload file must use POST request method!');
                }
                $this->restClient->addFile($key, $row['value']);
            }
        }
        $res = $this->restClient->execute();
        return $this->_buildResponse($res, $responseType);
    }

    /**
     * 获取返回数据
     * 目前只支持json
     * 
     * @return ResponseInterface
     */
    protected function _buildResponse($res, $type = 'json')
    {
        $ns = '\\Resttest\\Response\\';
        $type = ucwords(strtolower($type));
        $cls = $ns . $type . 'Response';
        return new $cls($res);
    }

    /**
     * set cookie path
     * 
     * @param string $cookiePath cookie path
     * 
     * @return Request
     */
    public function isLoginRequest($cookiePath)
    {
        $this->restClient->saveCookie($cookiePath);
        return $this;
    }
}