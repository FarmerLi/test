<?php
class RunAbleTest extends PHPUnit_Framework_TestCase
{
    /**
     * 测试是否能够执行子目录unittest
     */
    public function testSubDir()
    {
        $this->assertTrue(true);
    }

    public function testExec()
    {
        $this->assertTrue(method_exists(new RunAble, 'exec'));
    }
	/**
	 * @dataProvider p
	 */
	public function testP($a, $b)
	{
		
	}

	public function p()
	{
		return [
			[1,2],
			[2,3]
		];
	}

    public function tearDown()
    {
        echo 1;
    }
}
