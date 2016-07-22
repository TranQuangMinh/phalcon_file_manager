<?php
$router->add('/', array(
    'module' => 'filemanager',
    'controller' => 'main',
    'action' => 'index'
))->setName('index');

$router->add('/login{query:(/.*)*}', array(
    'module' => 'filemanager',
    'controller' => 'base',
    'action' => 'login'
))->setName('login');

$router->add('/logout', array(
    'module' => 'filemanager',
    'controller' => 'base',
    'action' => 'logout'
))->setName('logout');

// API
$router->add('/folder-list{query:(/.*)*}', array(
    'module' => 'filemanager',
    'controller' => 'api',
    'action' => 'getFolderList'
))->setName('get_folder_list');

$router->add('/folder-entry{query:(/.*)*}', array(
    'module' => 'filemanager',
    'controller' => 'api',
    'action' => 'getFolderEntry'
))->setName('get_folder_entry');


$router->add('/add-dir{query:(/.*)*}', array(
    'module' => 'filemanager',
    'controller' => 'api',
    'action' => 'addDir'
))->setName('add_dir');

$router->add('/delete{query:(/.*)*}', array(
    'module' => 'filemanager',
    'controller' => 'api',
    'action' => 'delete'
))->setName('delete');

$router->add('/upload-file{query:(/.*)*}', array(
    'module' => 'filemanager',
    'controller' => 'api',
    'action' => 'uploadFile'
))->setName('upload_file');

$router->add('/detail{query:(/.*)*}', array(
    'module' => 'filemanager',
    'controller' => 'main',
    'action' => 'detailFile'
))->setName('detail_file');

$router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
$router->notFound(array(
    'module' => 'filemanager',
    'controller' => 'error',
    'action' => 'error404'
));
