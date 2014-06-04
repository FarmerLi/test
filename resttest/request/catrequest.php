<?php
/**
 * cat rest test request driver
 *
 * @author Farmer.Li <me@farmerli.com>
 */
namespace Resttest\Request;

use Resttest\Request\Request,
    Resttest\User;

/**
 * cat rest test request driver
 */
class CatRequest extends Request
{
    /**
     * 构造
     *
     * @return void
     */
    public function __construct($controller, $method)
    {
        parent::__construct();
        $target = $this->_buildTarget($controller, $method);
        parent::setTarget($target);
    }

    public function addParams($params)
    {
        if (!is_array($params)) {
            throw new \Exception ('params must is array');
        }
        foreach ($params as $key => $value) {
            $this->addParam($key, $value);
        }
        return $this;
    }

    /**
     * 组装url
     * 
     * @param string $controller controller
     * @param string $method     method
     * 
     * @return string
     */
    private function _buildTarget($controller, $method)
    {
        $baseurl = getConfig()['baseurl'];
        $params = 'm=' . $controller . '&f=' . $method . '&t=json';
        $target = '';
        if (strpos('?', $baseurl) !== false) {
            $target = $baseurl . $params;
        } else {
            $target = $baseurl . '?' . $params;
        }
        return $target;
    }

    /**
     * 发出请求
     *
     * @param $string $responseType response type
     * 
     * @return Response
     */
    public function send($responseType = 'json')
    {
        $cookieFile = User::cookiePath();
        if (file_exists($cookieFile)) {
            $this->restClient->sendCookie(User::cookiePath());
        }
        return parent::send($responseType);
    }   
}