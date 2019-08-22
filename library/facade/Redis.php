<?php
namespace geek1992\tools\library\facade;

use geek1992\tools\library\Facade;

/**
 * @author: Geek <zhangjinlei01@bilibili.com>
 * @method \Redis  getInstance(array $options = []) static 获取redis实例
 * @method  string getKey(string $namespace, ?string $prefix = null) static 获取缓存的key
 */
class Redis extends Facade
{
    protected static function getFacadeClass()
    {
        return 'redis';
    }
}