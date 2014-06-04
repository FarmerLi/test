<?php
namespace Resttest;

use Resttest\Request\Request;

class User
{
    private $_user = [];

    /**
     * 构造
     *
     * @return void
     */
    private function __construct()
    {

    }

    /**
     * 单例
     * 
     * @return User
     */
    public function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new User;
        }
        return $instance;
    }

    /**
     * get user
     * 
     * @return array
     */
    public function get()
    {
        return $this->_user;
    }

    /**
     * login for gateway
     * 
     * @param string $username username
     * @param string $password password
     * 
     * @return boolean
     */
    public function login($username, $password)
    {
        $this->logout();
        $config = getConfig();
        $target = $config['gateway'] . '?t=json&m=login&f=userLogin';
        $request = new Request();
        $response = $request->setTarget($target)
            ->addParam('user_name', $username)
            ->addParam('password', $password)
            ->isLoginRequest(self::cookiePath())
            ->send('json');
        if ($response->getCode() != '200' && $response->getBody()['code'] != '0') {
            throw new Exception ('login faild : ' . $response->getBody()['message']);
        }
    }

    /**
     * logout
     * 
     * @return void
     */
    public function logout()
    {
        @ unlink($this->cookiePath());
        $this->user = [];
    }

    /**
     * validate login
     * 
     * @return boolean
     */
    public function isLogin()
    {
        return empty($this->user) ? false : true;
    }

    static public function cookiePath()
    {
        if (!file_exists(TEST_TMP_PATH)) {
            mkdir(TEST_TMP_PATH, 0777);
        }
        return TEST_TMP_PATH . DIRECTORY_SEPARATOR . 'cookie.txt';
    }
}