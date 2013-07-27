<?php
class Param
{
	public $a = 1;
}
class TestClass
{
	private $p;

	public function __construct(Param $p)
	{
		$this->p = $p;
	}

	function testMethod()
	{
		print "hi, i\'m running!\n p is : " . $this->p->a;
	}
}

class ProClass {}

/**
 * @method TestClass createClass(Param $p) $a
 * @property ProClass $pc
 */
class myclass
{
	use DependencyInjection;

	public function run()
	{
		var_dump($this->a, $this->pc);
	}
}

class AnotationManage
{
	private $anotationStore = null;

	private function __construct()
	{
		//@todo read cache
		if (null === $this->anotationStore) {
			$this->anotationStore = [];
		}
	}

	public function getInstance()
	{
		static $instance = null;
		if (null === $instance) {
			$instance = new self;
		}
		return $instance;
	}

	static public function get($class)
	{
		if (!isset($this->anotationStore[$class])) {
			$this->anotationStore[$class] = new anotation($class);
		}
		return $this->anotationStore[$class];

	}

	public function getAnotationFactory()
	{

	}
}

class AnotationFactory
{
	public function create($class)
	{
		return new Anotation($class);
	}
}

class Anotation
{
	private $_class = '';

	private $_antations = [];

	private $_parser = null;

	public function __construct($class)
	{
		$this->_class = $class;
		$this->_antations = $this->getParser($class)->parse();
	}

	public function getProperty()
	{
		
	}

	public function getParser($className)
	{
		return $this->_parser ? $this->_parser : new AnotationParser($className);
	}

	public function setParser(AnotationParserInterface $parser)
	{
		$this->_parser = $parser;
	}

}

interface AnotationParserInterface
{
	public function parse();
}

class AnotationParser implements AnotationParser
{
	public function parse()
	{
		return [];
	}
}

trait DependencyInjection
{
	private $_anotationManage = null;

	private $_dependency = [];

	private $_injector = null;

	public function __get($name)
	{
		if (isset($this->_dependency[$name])) {
			return $this->_dependency[$name];
		}
		$property = $this->getAnotationManage()
			->get($class)
			->getProperty($name);
		if (!$property) {
			throw new Exception();
		}

	}

	public function __set($name, $value)
	{

	}

	public function __call($func, $args)
	{

	}

	public function getAnotationManage()
	{
		return $this->_anotationManage ?
			$this->_anotationManage :
			AnotationManage::getInstance();
	}

	public function setAnotationManage(AnotationManageInterFace $manage)
	{
		$this->_anotationManage = $manage;
	}
}