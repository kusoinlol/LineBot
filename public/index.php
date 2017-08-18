<?php
use Phalcon\DI\FactoryDefault;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Application;
use \Phalcon\Mvc\View;
use Phalcon\Mvc\Model\Manager as ModelsManager;

try {
    date_default_timezone_set("Asia/Taipei");
    error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);

    $config = include __DIR__ . '/../app/config/define.php';

    // Read auto-loader
    $loader = include APP_PATH . '/config/loader.php';

    // Read the configuration
    $config = include APP_PATH . '/config/config.php';

    // Read routes
    $router = include APP_PATH . '/config/router.php';

    // Read services
    $di = new FactoryDefault();
    include APP_PATH . '/config/service.php';
    // Setting
    $di->set('config', $config);
    $di->set('loader', $loader);
    $di->set('router', $router, true);

    $request = new Request();
    $di->set("request", $request);

    $response = new Response();
    $response->setContentType("application/json", "UTF-8");
    $di->set("response", $response);
    $di->set('modelsManager', function() {
      return new ModelsManager();
    });
    // Setting up the view component
    if (SUB_PATH == "webapp/") {
        $di->set('view', function () use ($config) {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir(APP_PATH . 'views/');
            $view->registerEngines(array(
                '.phtml' => 'Phalcon\Mvc\View\Engine\Php',
            ));
            return $view;
        }, true);

        // Handle the request
        $application = new \Phalcon\Mvc\Application($di);
        echo $application->handle()->getContent();
        exit();
    } //end if

    // Handle the request
    $application = new Application($di);
    $application->useImplicitView(false);
    echo $application->handle()->getContent();
} catch (Exception $e) {
    echo $e->getMessage();
} //end try