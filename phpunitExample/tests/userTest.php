<?php
class TestUser extends PHPUnit_Framework_TestCase
{
    public $user = null;

    public function setUp()
    {
        $this->user = new User('Man', 'man@none.com');
    }

    public function testUserInstance()
    {
        $this->assertInstanceOf('BaseUser', $this->user);
        $user = $this->user->getUser();
        $this->assertTrue($user['name'] != null);
        // 断言email的类型为string
        $this->assertInternalType('string', $user['email']);
    }

    /**
     * @test
     */
    public function isHuman()
    {
        $this->assertTrue($this->user->isHuman());
    }

    /**
     * 此方法依赖isHuman, 且由provider提供计算数据
     * 
     * @depends isHuman
     * @dataProvider provider
     */
    public function testThinkPlus($a, $b, $c)
    {
        $this->assertEquals($c, $this->user->thinkPlus($a, $b));
    }

    public function provider()
    {
        return [
            [1, 2, 3],
            [2, 3, 5]
        ];
    }

    /**
     * 捕获输出内容, 进行断言
     */
    public function testRun()
    {
        $this->expectOutputString('yes, I run fast!');
        // $this->expectOutputRegex('/yes/'); 使用正则
        $this->user->run();
    }

    /**
     * 测试异常
     *
     * @expectedException Exception
     * @expectedExceptionMessage sorry
     */
    public function testEat()
    {
        $this->user->eat();
    }
}
?>