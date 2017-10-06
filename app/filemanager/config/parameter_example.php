<?php
$parameters = array();

$parameters = array(
    'cache' => array(
        'lifetime' => 300,
        'prefix' => '_mbn_admin_',
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
            'prefix' => 'mbn_',
            'lifetime' => '31536000'
        )
    ),
    'limit' => array(
        'size' => 1000000, // 500kB,
        'width' => 1000,
        'height' => 1000,
        'jpeg_quality' => 90

    ),
    'volt' => array(
        'debug' => true,
        'stat' => true,
        'compiled_separator' => '_'
    ),

    'application' => array(
        'protocol' => 'http://',
        'pagination_limit' => '3',
        'base_url' => 'http://filemanager.dev/',
        'token' => '21232f297a57a5a743894a0e4a801fc3', //admin
        'token_user' => '21232f297a57a5a743894a0e4a801fc3' // admin,
        'upload_dir' => ROOT . '/web/filemanager/uploads/',
        'thumbnail_dir' => ROOT . '/web/filemanager/thumbnails/',
        'thumbnail_url' => 'http://filemanager.dev/thumbnails/',
        'upload_url' => 'http://filemanager.dev/uploads/',
        'allow_parent' => 'https://admin.domain.com/'
    ),
);
