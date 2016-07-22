<?php
date_default_timezone_set('Asia/Bangkok');
ini_set('display_errors', true);
error_reporting(E_ALL);

try {
    define('ROOT', realpath(dirname(dirname(dirname(__FILE__)))));

    $loader = new \Phalcon\Loader();
    $loader->registerDirs(array(
        ROOT . '/app/filemanager/',
        ROOT . '/app/data/source'
    ))->register();

    $di = new \Phalcon\DI\FactoryDefault();

    require_once ROOT . '/app/filemanager/config/parameter.php';
    $config = new \Phalcon\Config($parameters);
    $di->setShared('config', $config);

    $di->setShared('url', function() use ($config) {
        $url = new \Phalcon\Mvc\Url();
        $url->setBaseUri($config->application->base_url);
        return $url;
    });

    $di->setShared('router', function() {
        $router = new \Phalcon\Mvc\Router(false);

        require_once ROOT . '/app/filemanager/config/router.php';
        $router->removeExtraSlashes(true);

        return $router;
    });

    if ($config->cache->type == 'memcache') {
        $di->setShared('cache', function() use ($config) {
            $data_cache = new \Phalcon\Cache\Frontend\Data(array(
                'lifetime' => $config->cache->lifetime,
                'prefix' => $config->cache->prefix
            ));
            $cache = new \Phalcon\Cache\Backend\Memcache($data_cache, array(
                'host' => $config->cache->memcache->host,
                'port' => $config->cache->memcache->port,
                'persistent' => $config->cache->memcache->persistent
            ));
            return $cache;
        });
    } elseif ($config->cache->type == 'redis') {
        $di->setShared('cache', function() use ($config) {
            $data_cache = new \Phalcon\Cache\Frontend\Data(array(
                'lifetime' => $config->cache->lifetime,
                'prefix' => $config->cache->prefix
            ));
            $cache = new \Phalcon\Cache\Backend\Redis($data_cache, array(
                'host' => $config->cache->redis->host,
                'port' => $config->cache->redis->port,
                'auth' => $config->cache->redis->auth,
                'persistent' => $config->cache->redis->persistent
            ));
            return $cache;
        });
    } else {
        $di->setShared('cache', function() use ($config) {
            $data_cache = new \Phalcon\Cache\Frontend\Data(array(
                'lifetime' => $config->cache->lifetime,
                'prefix' => $config->cache->prefix
            ));
            $cache = new \Phalcon\Cache\Backend\Apc($data_cache, array());
            return $cache;
        });
    }

    $di->setShared('modelsMetadata', function() use ($config) {
        $meta_data = new \Phalcon\Mvc\Model\MetaData\Files(array(
            'metaDataDir' => ROOT . '/cache/data/metadata/',
            'prefix' => $config->cache->metadata->prefix,
            'lifetime' => $config->cache->metadata->lifetime
        ));
        return $meta_data;
    });

    if ($config->cache->type == 'memcache') {
        $di->setShared('modelsCache', function() use ($config) {
            $data_cache = new \Phalcon\Cache\Frontend\Data(array(
                'lifetime' => $config->cache->lifetime,
                'prefix' => $config->cache->prefix
            ));
            $cache = new \Phalcon\Cache\Backend\Memcache($data_cache, array(
                'host' => $config->cache->memcache->host,
                'port' => $config->cache->memcache->port,
                'persistent' => $config->cache->memcache->persistent
            ));
            return $cache;
        });
    } elseif ($config->cache->type == 'redis') {
        $di->setShared('modelsCache', function() use ($config) {
            $data_cache = new \Phalcon\Cache\Frontend\Data(array(
                'lifetime' => $config->cache->lifetime,
                'prefix' => $config->cache->prefix
            ));
            $cache = new \Phalcon\Cache\Backend\Redis($data_cache, array(
                'host' => $config->cache->redis->host,
                'port' => $config->cache->redis->port,
                'auth' => $config->cache->redis->auth,
                'persistent' => $config->cache->redis->persistent
            ));
            return $cache;
        });
    } else {
        $di->setShared('modelsCache', function() use ($config) {
            $data_cache = new \Phalcon\Cache\Frontend\Data(array(
                'lifetime' => $config->cache->lifetime,
                'prefix' => $config->cache->prefix
            ));
            $cache = new \Phalcon\Cache\Backend\Apc($data_cache, array());
            return $cache;
        });
    }

    $di->setShared('security', function() {
        $security = new \Phalcon\Security();
        return $security;
    });

    $di->setShared('session', function() {
        $session = new \Phalcon\Session\Adapter\Files();
        $session->start();
        return $session;
    });

    $di->setShared('crypt', function() use ($config) {
        $crypt = new \Phalcon\Crypt();
        $crypt->setKey($config->application->cookie_key);
        return $crypt;
    });

    $di->setShared('cookies', function() {
        $cookies = new \Phalcon\Http\Response\Cookies();
        $cookies->useEncryption(false);
        return $cookies;
    });

    $di->setShared('flashSession', function() {
        return new \Phalcon\Flash\Session(array(
            'error' => 'alert alert-danger',
            'success' => 'alert alert-success',
            'warning' => 'alert alert-warning'
        ));
    });

    $di->setShared('dispatcher', function() {
        $dispatcher = new \Phalcon\Mvc\Dispatcher();
        $dispatcher->setDefaultNamespace('MINI\Filemanager\Controller\\');

        $events_manager = new \Phalcon\Events\Manager();
        $events_manager->attach('dispatch', function($event, $dispatcher, $exception) {
            $type = $event->getType();
            if ($type == 'beforeException') {
                if ($exception->getCode() == \Phalcon\Mvc\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND || $exception->getCode() == \Phalcon\Mvc\Dispatcher::EXCEPTION_ACTION_NOT_FOUND) {
                    $dispatcher->forward(array(
                        'module' => 'filemanager',
                        'controller' => 'error',
                        'action' => 'error404'
                    ));
                    return false;
                } else {
                    $dispatcher->forward(array(
                        'module' => 'filemanager',
                        'controller' => 'error',
                        'action' => 'error',
                        'params' => array($exception)
                    ));
                    return false;
                }
            }
        });

        $dispatcher->setEventsManager($events_manager);
        return $dispatcher;
    });

    $di->setShared('logger', function() {
        $logger = new \Phalcon\Logger\Adapter\File(ROOT . '/log/filemanager_error.log');
        return $logger;
    });

    $di->setShared('postLog', function() {
        $logger = new \Phalcon\Logger\Adapter\File(ROOT . '/log/filemanager_post.log');
        return $logger;
    });

    $application = new \Phalcon\Mvc\Application($di);
    $application->registerModules(array(
        'filemanager' => array(
            'className' => 'MINI\Filemanager\Module',
            'path' => ROOT . '/app/filemanager/Module.php'
        ),
        'data' => array(
            'className' => 'ITECH\Data\Module',
            'path' => ROOT . '/app/data/Module.php'
        )
    ));

    echo $application->handle()->getContent();
} catch (\Exception $e) {
    throw new \Phalcon\Exception($e->getMessage());
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage());
}
