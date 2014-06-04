<?php
/**
 * 标准加载器
 *
 * @author Farmer.Li <me@farmerli.com>
 */
class Loader
{
    const NS_SEPARATOR = '\\';

    private $_base = '.';

    private $_rules = [];

    /**
     * 构造
     * 
     * @param string $base base dir
     *
     * @return void
     */
    public function __construct($base)
    {
        $this->_base = $base;
    }

    /**
     * 加载
     * 
     * @return void
     */
    function autoload($cls)
    {
        $path = strtolower(str_replace(self::NS_SEPARATOR, DIRECTORY_SEPARATOR, $cls));

        foreach ($this->_rules as $key => $value) {
            if (stripos($path, $key) === 0) {
                $file = str_replace($key, $value, $path) . '.php';
                if (file_exists($file)) {
                    include_once $file;
                    return ;
                }
            }
        }
        $file = $this->_base . DIRECTORY_SEPARATOR . $path . '.php';
        if (file_exists($file)) {
            include_once $file;
            return ;
        }
    }

    /**
     * 加入一条载入规则
     * 设定指定的名称空间对应的路径
     * 
     * @param [type] $key [description]
     * @param [type] $dir [description]
     *
     * @return self
     */
    function addRule($key, $dir)
    {
        $key = strtolower($key);
        $this->_rules[$key] = $dir;
        return $this;
    }
}