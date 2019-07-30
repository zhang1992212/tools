<?php
namespace geek1992\tools\library;

/**
 * REDIS 缓存类
 * @author: Geek <zhangjinlei01@bilibili.com>
 */
class Redis
{
    private const KEY = 'REDIS';
    /**
     * @var \Redis
     */
    private static $instance = [];

    private static $config = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'auth'       => '',
        'select'     => 0,
        'timeout'    => 0,
        'expire'     => 0,
        'persistent' => false,
        'prefix'     => '',
    ];

    private function __construct()
    {

    }

    private function __clone()
    {
    }

    /**
     * 根据不同的数组参数生成不同的key
     * @param array $key
     * @return string
     */
    private static function getInstanceKey(array $key = []):string
    {
        return md5(static::KEY . md5(http_build_query($key)));
    }

    /**
     * 根据不同的配置项生成不同REDIS实例
     * @param array $options
     * @return \Redis
     */
    public static function getInstance(array $options = []):\Redis
    {
        static::$config = array_merge(static::$config, $options);
        $key = static::getInstanceKey(static::$config);

        if (empty(static::$instance) || !isset(static::$instance[$key])){
            static::$instance[$key] = static::connect();
        }
        return static::$instance[$key];
    }

    /**
     * 连接redis
     * @return bool
     */
    private static function connect(): ?\Redis
    {
        try{
            $config = static::getConfig();

            $redis = new \Redis();
            if ($config['persistent']) {
                $redis->pconnect($config['host'], $config['port'], $config['timeout'], 'persistent_id_' . $config['select']);
            } else {
                $redis->connect($config['host'], $config['port'], $config['timeout']);
            }
            if ($config['auth']) {
                $redis->auth($config['auth']);

            }
            if ($config['select']) {
                $redis->select($config['select']);
            }

            return $redis;
        } catch (\Exception $e){
            return null;
        }
    }

    /**
     * 获取配置项信息
     * @param string $name
     * @return array|mixed
     */
    private static function getConfig(string $name = '')
    {
        return $name ? static::$config[$name] : static::$config;
    }

    /**
     * 获取缓存的key
     * @param string $namespace
     * @param string|null $prefix
     * @return string
     */
    public static function getKey(string $namespace, ?string $prefix = null): string
    {
        return trim(strtoupper(trim(strtr($namespace, ['\\' => ':']), ':') . ':' . ($prefix ? trim($prefix, ':') . ':' : '')), ":");
    }
}