<?php
/**
 * 观察者模式
 * 
 * User 为被观察者，当User改变时，发送通知到EmailNotify 和 MessageNotify
 * @author Farmer.Li <me@farmerli.com>
 */
class User implements SplSubject
{
	private $_user = [];

	private $_observers = null;

	/**
	 * construct
	 * 
	 * @param \Array $user user data
	 */
	public function __construct($user)
	{
		$this->_user = $user;
		$this->_observers = new SplObjectStorage();
	}

	/**
	 * add a observer
	 * 
	 * @param SplObserver $ob observer
	 * 
	 * @return boolean
	 */
	public function attach(SplObserver $ob)
	{
		if (!$this->_observers->contains($ob)) {
			$this->_observers->attach($ob);
		}
		return true;
	}

	/**
	 * remove a observer
	 * @param SplObserver $ob observer
	 * 
	 * @return boolean
	 */
	public function detach(SplObserver $ob)
	{
		if ($this->_observers->contains($ob)) {
			$this->_observers->detach($ob);
		}
		return true;
	}

	/**
	 * send notify to all observers
	 * 
	 * @return void
	 */
	public function notify()
	{
		foreach ($this->_observers as $observer) {
			$observer->update($this);
		}
	}

	/**
	 * change user name
	 * 
	 * @param string $name user name
	 * 
	 * @return void
	 */
	public function changeName($name)
	{
		$this->_user['name'] = $name;
		$this->notify();
	}

	/**
	 * get user name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->_user['name'];
	}
}


/**
 * email notify library
 *
 * @author Farmer.Li <me@farmerli.com>
 */
class EmailNotify implements SplObserver
{
	/**
	 * accept a subject notify, do something..
	 * 
	 * @param  SplSubject $s subject
	 * 
	 * @return void
	 */
	public function update(SplSubject $s)
	{
		$name = $s->getName();
		echo "it's a new Emial, because you name modify to " . $name . PHP_EOL;
	}
}

/**
 * Message notify library
 */
class MessageNotify implements SplObserver
{
	/**
	 * accept a subject notify, do something..
	 * 
	 * @param  SplSubject $s subject
	 * 
	 * @return void
	 */
	public function update(SplSubject $s)
	{
		$name = $s->getName();
		echo "this's a new message, because you name modify to " . $name . PHP_EOL;
	}
}
// create a user
$user = new User(['name' => 'test', 'email' => 'test@test.com']);

// add observers
$user->attach(new EmailNotify());
$user->attach(new MessageNotify());

// change name
$user->changeName('newName');

?>