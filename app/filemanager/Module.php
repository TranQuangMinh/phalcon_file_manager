<?php
namespace MINI\Filemanager;

class Module
{
    public function registerAutoloaders()
    {
        $loader = new \Phalcon\Loader();

        $loader->registerNamespaces(array(
            'MINI\Filemanager\Controller' => ROOT . '/app/filemanager/controller/',
            'MINI\Filemanager\Component' => ROOT . '/app/filemanager/component/',
            'MINI\Data\Lib'         => ROOT . '/app/data/lib/'
        ));
        $loader->register();
    }

    public function registerServices($di)
    {
        $config = $di->getService('config')->getDefinition();

        $di->setShared('volt', function($view, $di) use ($config) {
            $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

            $volt->setOptions(array(
                'compiledPath' => ROOT . '/cache/filemanager/volt/',
                'compiledSeparator' => $config->volt->compiled_separator,
                'compileAlways' => (bool)$config->volt->debug,
                'stat' => (bool)$config->volt->stat
            ));

            $compiler = $volt->getCompiler();

            $compiler->addFunction('in_array', 'in_array');
            $compiler->addFunction('http_build_query', 'http_build_query');
            $compiler->addFunction('uniqid', 'uniqid');
            $compiler->addFunction('strtotime', 'strtotime');
            $compiler->addFunction('date', 'date');
            $compiler->addFunction('nl2br', 'nl2br');
            $compiler->addFunction('rand', 'rand');
            $compiler->addFunction('array_merge', 'array_merge');

            return $volt;
        });

        $di->setShared('view', function() {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir(ROOT . '/app/filemanager/view/');
            $view->registerEngines(array(
                '.volt' => 'volt'
            ));

            return $view;
        });

    }
}
