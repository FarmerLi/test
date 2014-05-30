<?php
abstract class Emun
{

	public static function _getReflectionClass()
	{
		$class = get_called_class();
		$ref = new reflectionClass($class);
		$a = ReflectionClass::export(new static(), true);
		var_dump($a);exit;
		foreach ($ref->getConstants() as $key => $row) {
			$p = $ref->getConstant($key);
			$a = self::parseComment($p->getDocComment());
		}

	}

	public static function parseComment($comment)
	{
		preg_match_all('/\@(\w+)\s+(\w+)(\s+(\w+))?/', $comment, $a);
		var_dump($a);
	}

	public static function toArray()
	{
		$class = static::_getReflectionClass();
	}

	public static function toOptions()
	{

	}

	public function exsits($val)
	{

	}


}


class TypeEmun extends Emun
{
	/**
	 * 类型1
	 */
	const ONE = 'ONE';

	/**
	 * 类型2
	 */
	const TWO = 'TWO';

	/**
	 * test
	 *
	 * @title test test111
	 * @var integer
	 */
	public $a = 1;
}

TypeEmun::toArray();

