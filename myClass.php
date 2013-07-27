<?php
abstract class ClassBase
{
	public $name = '';

	public $email = '';

	abstract public function setName($name);

	abstract public function getName();

	public function getUser()
	{
		return array(
			'name' => $this->name,
			'email' => $this->email
		);
	}
}

class MyClass extends ClassBase
{

	const VAR1 = 1;

	public function __construct($name, $email)
	{
		$this->name = $name;
		$this->email = $email;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}


	static public function testMockStatic()
	{
		return [
			'var1' => self::VAR1
		];
	}

	public function testMockStatica()
	{
		return [1,2];
	}
}

class testClass 
{
	public function dump()
	{
		return $this->iClass->testMockStatic()['var1'];
	}

	public function set($class)
	{
		$this->iClass = $class;
	}

	public function get($class)
	{
		return $this->iClass == null ? new MyClass() : $this->iClass;
	}

}
