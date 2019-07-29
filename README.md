# redis
<<<<<<< HEAD
```$xslt
=======
>>>>>>> b0995e66f687a198ad6603ce52fbf1cc15932780
$config = [
    'host'       => '127.0.0.1',
    'port'       => 6379,
    'auth'   => '',
    'select'     => 0,
    'timeout'    => 0,
    'expire'     => 0,
    'persistent' => false,
    'prefix'     => '',
];
$redis = \geek1992\redis\library\Redis::getInstance($config);

$redis->set('a', 1);

print_r($redis->get('a'));
<<<<<<< HEAD
```

=======
>>>>>>> b0995e66f687a198ad6603ce52fbf1cc15932780
