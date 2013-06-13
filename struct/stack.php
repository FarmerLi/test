<?php
class stack
{
	private $_data = null;

	public function __construct()
	{
		$this->_data = [];
	}

	public function __destory()
	{
		$this->clear();
		unset($this);
	}

	public function destory()
	{
		$this->__destory();
	}

	public function clear()
	{
		$this->_data = [];
	}

	public function isEmpty()
	{
		return empty($this->_data);
	}

	public function push($value)
	{
		array_push($this->_data, $value);
	}

	public function pop()
	{
		return array_pop($this->_data);
	}

	public function length()
	{
		return count($this->_data);
	}

}