<?php
abstract class BaseUser
{
    protected $name = '';

    protected $email = '';

    abstract public function getUser();

    abstract public function run();

    abstract public function eat();

    public function isHuman()
    {
        return true;
    }

    abstract public function thinkPlus($a, $b);
}
?>