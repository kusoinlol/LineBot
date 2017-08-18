<?php
use Phalcon\DI\FactoryDefault;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Application;
use \Phalcon\Mvc\View;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Loader;

date_default_timezone_set("Asia/Taipei");
$config = include __DIR__ . '/config/define.php';
// Read auto-loader
$loader = include APP_PATH . '/config/loader.php';
// Read the configuration
$config = include APP_PATH . '/config/config.php';
// Read routes
$router = include APP_PATH . '/config/router.php';
// Read services

$di = new CliDI();
include APP_PATH . '/config/service.php';
// Setting
$di->set('config', $config);
$di->set('loader', $loader);

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "task/",
    ]
);

$loader->register();


// Create a console application
$console = new ConsoleApp();

$console->setDI($di);



/**
 * Process the console arguments
 */
$arguments = [];

foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments["task"] = $arg;
    } elseif ($k === 2) {
        $arguments["action"] = $arg;
    } elseif ($k >= 3) {
        $arguments["params"][] = $arg;
    }
}

try {
    // Handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
}
exit(255);