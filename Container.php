<?php
namespace geek1992\tools;

use geek1992\tools\exception\ClassNotFoundException;
use geek1992\tools\library\Redis;
use ReflectionClass;

/**
 * @author: Geek <zhangjinlei01@bilibili.com>
 * @property Redis $redis
 */
class Container
{
    /**
     * 容器对象实例
     * @var Container
     */
    protected static $instance;

    /**
     * 容器中的对象实例
     * @var array
     */
    protected $instances = [];

    /**
     * 容器标识别名
     * @var array
     */
    protected $name = [];


    /**
     * 容器绑定标识
     * @var array
     */
    protected $bind = [
        'redis'                   => Redis::class,
    ];

    /**
     * 获取当前容器的实例（单例）
     * @access public
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }


    /**
     * 获取容器中的对象实例
     * @access public
     * @param  string        $abstract       类名或者标识
     * @param  array|true    $vars           变量
     * @param  bool          $newInstance    是否每次创建新的实例
     * @return object
     */
    public static function get($abstract, $vars = [], $newInstance = false)
    {
        return static::getInstance()->make($abstract, $vars, $newInstance);
    }

    public function make($abstract, $vars = [], $newInstance = false)
    {
        $abstract = $this->name[$abstract] ?? $abstract;

        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (isset($this->bind[$abstract])) {
            $concrete = $this->bind[$abstract];

            if ($concrete instanceof \Closure) {
                $object = $this->invokeFunction($concrete, $vars);
            } else {
                $this->name[$abstract] = $concrete;
                return $this->make($concrete, $vars, $newInstance);
            }
        } else {
            $object = $this->invokeClass($abstract, $vars);
        }

        if (!$newInstance) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    public function invokeClass($class, $vars = [])
    {
        try {
            $reflect = new ReflectionClass($class);

//            if ($reflect->hasMethod('__make')) {
//                $method = new ReflectionMethod($class, '__make');
//
//                if ($method->isPublic() && $method->isStatic()) {
//                    $args = $this->bindParams($method, $vars);
//                    return $method->invokeArgs(null, $args);
//                }
//            }

            $constructor = $reflect->getConstructor();
//            $args = $constructor ? $this->bindParams($constructor, $vars) : [];
            $args = $vars;
            return $reflect->newInstanceArgs($args);

        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException('class not exists: ' . $class, $class);
        }
    }
}