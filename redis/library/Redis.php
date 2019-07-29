<?php
namespace geek1992\redis\library;

/**
 * @author: Geek <zhangjinlei01@bilibili.com>
 */
class Redis
{
    /**
     * @var \Redis
     */
    private static $instance = null;

    protected static $config = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'auth'   => '',
        'select'     => 0,
        'timeout'    => 0,
        'expire'     => 0,
        'persistent' => false,
        'prefix'     => '',
    ];

    private function __construct(array $options = [])
    {

    }

    protected function __clone()
    {
    }

    public static function getInstance(array $options = []):\Redis
    {
        if (static::$instance === null){
            if (!empty($options)) {
                static::$config = array_merge(static::$config, $options);
            }
            static::connect();
        }
        return static::$instance;
    }

    /**
     * 连接redis
     * @return bool
     */
    private static function connect(): bool
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

            static::$instance = $redis;
            return true;
        } catch (\Exception $e){
            return false;
        }
    }

    protected static function getConfig(string $name = '')
    {
        return $name ? static::$config[$name] : static::$config;
    }

}