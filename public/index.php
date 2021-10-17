<?php

use App\Framework\Application\Application;
use App\Framework\Container\Container;
use App\Framework\Router\RouterInterface;

error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

$container = Container::getInstance();
require __DIR__ . '/../config/services.php';

$router = $container->instantiate(RouterInterface::class);
require __DIR__ . '/../config/routes.php';

Application::getInstance()
    ->setContainer($container)
    ->run()
;
