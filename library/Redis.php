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
    private $instance = [];

    private $config = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'auth'       => '',
        'select'     => 0,
        'timeout'    => 0,
        'expire'     => 0,
        'persistent' => false,
        'prefix'     => '',
    ];
    

    /**
     * 根据不同的数组参数生成不同的key
     * @param array $key
     * @return string
     */
    private function getInstanceKey(array $key = []):string
    {
        return md5(static::KEY . md5(http_build_query($key)));
    }

    /**
     * 根据不同的配置项生成不同REDIS实例
     * @param array $options
     * @return \Redis
     */
    public function getInstance(array $options = []):\Redis
    {
        $config = array_merge($this->config, $options);
        $key = $this->getInstanceKey($config);

        if (empty($this->instance) || !isset($this->instance[$key]) || empty($this->instance[$key])){
            $this->instance[$key] = $this->connect($config);
        }
        return $this->instance[$key];
    }

    /**
     * 连接redis
     * @param array $config
     * @return \Redis|null
     */
    private function connect(array $config = []): ?\Redis
    {
            $redis = new \Redis();
            if ($config['persistent']) {
                $redis->pconnect($config['host'], (int)$config['port'], $config['timeout'], 'persistent_id_' . $config['select']);
            } else {
                $redis->connect($config['host'], (int)$config['port'], $config['timeout']);
            }
            if ($config['auth']) {
                $redis->auth($config['auth']);
            }

            if ($config['select']) {
                $redis->select($config['select']);
            }
            return $redis;
    }

    /**
     * 获取缓存的key
     * @param string $namespace
     * @param string|null $prefix
     * @return string
     */
    public function getKey(string $namespace, ?string $prefix = null): string
    {
        return trim(strtoupper(trim(strtr($namespace, ['\\' => ':']), ':') . ':' . ($prefix ? trim($prefix, ':') . ':' : '')), ":");
    }

}