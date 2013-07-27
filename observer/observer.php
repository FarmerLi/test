<?php
class User implements SplSubject
{
	private $_user = [];

	private $_observers = null;

	public function __construct($user)
	{
		$this->_user = $user;
		$this->_observers = new SplObjectStorage();
	}

	public function attach(SplObserver $ob)
	{
		if (!$this->_observers->contains($ob)) {
			$this->_observers->attach($ob);
		}
		return true;
	}

	public function detach(SplObserver $ob)
	{
		if ($this->_observers->contains($ob)) {
			$this->_observers->detach($ob);
		}
		return true;
	}
	public function notify()
	{
		foreach ($this->_observers as $observer) {
			$observer->update($this);
		}
	}

	public function changeName($name)
	{
		$this->_user['name'] = $name;
		$this->notify();
	}

	public function getName()
	{
		return $this->_user['name'];
	}
}


class EmailNotify implements SplObserver
{
	public function update(SplSubject $s)
	{
		$name = $s->getName();
		echo "it's a new Emial, because you name modify to " . $name . PHP_EOL;
	}
}

class MessageNotify implements SplObserver
{
	public function update(SplSubject $s)
	{
		$name = $s->getName();
		echo "this's a new message, because you name modify to " . $name . PHP_EOL;
	}
}

$user = new User(['name' => 'test', 'email' => 'test@test.com']);
$user->attach(new EmailNotify());
$user->attach(new MessageNotify());
$user->changeName('newName');

?>