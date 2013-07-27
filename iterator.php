<?php
class testArray implements ArrayAccess,Iterator
{
	private $_data;

	public function __construct($data)
	{
		echo __METHOD__ . "\n";
		$this->_data = $data;
	}

	public function offsetExists($offset)
	{
		echo __METHOD__ . "\n";
		return isset($this->_data[$offset]);
	}

	public function offsetGet($offset)
	{
		echo __METHOD__ . "\n";
		$data = $this->_data[$offset];
		return strtoupper($data);
	}

	public function offsetSet($offset, $value)
	{
		echo __METHOD__ . "\n";
		$this->_data[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		echo __METHOD__ . "\n";
		unset($this->_data[$offset]);
	}

	/*--------------------- implements Iterator -------------------*/

	private $_cursor = 0;

	public function key()
	{
		echo __METHOD__ . "\n";
		return $this->_cursor;
	}

	public function current()
	{
		echo __METHOD__ . "\n";
		return $this->_data[$this->_cursor];
	}

	public function next()
	{
		echo __METHOD__ . "\n";
		++ $this->_cursor;
	}

	public function rewind()
	{
		echo __METHOD__ . "\n";
		$this->_cursor = 0;
	}

	public function valid()
	{
		echo __METHOD__ . "\n";
		return isset($this->_data[$this->_cursor]);
	}


}

$test = new testArray(['a', 'b']);
foreach ($test as $row) {
	echo $row . "\n";
}
echo $test[0] . "\n";
?>