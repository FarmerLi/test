<?php
class User extends BaseUser
{
    /**
     * 构造函数
     * 
     * @param string $name  名称
     * @param string $email 邮箱
     *
     * @return void
     */
    public function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function getUser()
    {
        return [
            'name' => $this->name,
            'email' => $this->email
        ];
    }

    public function run()
    {
        (new RunAble())->exec();
    }

    public function eat()
    {
        throw new Exception ('sorry, I am a mute, Can\'t talk');
    }

    public function thinkPlus($a, $b)
    {
        return $a + $b;
    }
}
?>