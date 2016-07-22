<?php
$parameters = array();

$parameters = array(
    'cache' => array(
        'lifetime' => 300,
        'prefix' => '_file_',
        'type' => 'apc',
        'memcache' => array(
            'host' => '127.0.0.1',
            'port' => '11211',
            'persistent' => false
        ),
        'redis' => array(
            'host' => '127.0.0.1',
            'port' => '6379',
            'auth' => 'redis',
            'persistent' => false
        ),
        'metadata' => array(
            'prefix' => 'file_',
            'lifetime' => '31536000'
        )
    ),

    'volt' => array(
        'debug' => true,
        'stat' => true,
        'compiled_separator' => '_'
    ),

    'application' => array(
        'protocol' => 'http://',
        'pagination_limit' => '3',
        'base_url' => 'http://filemanager.domain.com/',
        'token' => '9e95d128ee0d0fd3dc4bee95b279ae2b', // md5(user)
        'token_user' => '75412de6b9e3a2d750ace50770c52562', // md5(pass)
        'upload_dir' => ROOT . '/web/filemanager/uploads/',
        'upload_url' => 'http://filemanager.domain.com/uploads/',
        'allow_parent' => 'http://admin.domain.com/' //Admin show iframe and get data
    ),
);
