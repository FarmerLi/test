<?php
/**
 * 测试的测试用例。。。。
 *
 * @author Farmer.Li <me@farmerli.com>
 */
namespace Tests\test;

use Resttest\Request\CatRequest,
    Resttest\User;
    
/**
 * 测试的测试用例
 */
class TestTest extends \Resttest\CatTestCase
{
    public function setUp()
    {
        
    }

    /**
     * test run
     *
     * @return void
     */
    public function testNotLoginSearhRole()
    {
        User::getInstance()->logout();
        $r = new CatRequest('role', 'search');
        $response = $r->setMethod('POST')
            ->addParams([
                'name' => 'test_driver'
            ])->send();
        $this->assertEquals($response->getCode(), '401');
    }

    /**
     * test run
     *
     * @return void
     */
    public function testIsLoginSearhRole()
    {
        User::getInstance()->login('admin', '123456');
        $r = new CatRequest('role', 'search');
        $response = $r->setMethod('POST')
            ->addParams([
                'name' => 'test_driver'
            ])->send();
        $this->assertEquals($response->getCode(), '200');
        $this->assertEquals($response->getBody()['code'], 0);
    }
}