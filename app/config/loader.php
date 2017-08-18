<?php

if (file_exists(ROOT_PATH . 'vendor/autoload.php')) {
    include_once ROOT_PATH . 'vendor/autoload.php';
}

$loader = new \Phalcon\Loader();

$loader->registerDirs([APP_PATH . '/task/'])->register();

$loader->registerNamespaces(
    [
     'Anthony\LineBot\Controller'     => APP_PATH . '/controller/',
     'Anthony\LineBot\Controller\Api' => APP_PATH . '/controller/api',
     'Anthony\LineBot\Task'           => APP_PATH . "/task",
     "Anthony\LineBot\Model\Db"       => APP_PATH . "/model/db/",
     "Anthony\LineBot\Model\Dao"      => APP_PATH . "/model/dao/",
     "Anthony\LineBot\Model\Service"  => APP_PATH . "/model/service/",
    ]
)->register();

return $loader;