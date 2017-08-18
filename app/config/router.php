<?php
$router = new Phalcon\Mvc\Router(false);

require __DIR__ . '/router/ApiRouter.php';

$router->mount(new ApiRouter());

$router->notFound(
    [
     'namespace'  => 'Anthony\LineBot\Controller',
     'controller' => 'index',
     'action'     => 'notFound',
    ]
);

$routeCache = array();
$routes     = $router->getRoutes();
foreach ($routes as $route) {
    if (isset($routeCache[$route->getPattern()]) === false) {
        $routeCache[$route->getPattern()] = array();
    }

    array_push($routeCache[$route->getPattern()], $route->getHttpMethods());
}

foreach ($routeCache as $pattern => $methods) {
    $router->add(
        $pattern,
        array(
         'namespace'  => '',
         'controller' => 'index',
         'action'     => 'option',
         'options'    => $methods,
        ),
        'OPTIONS'
    );
}

return $router;