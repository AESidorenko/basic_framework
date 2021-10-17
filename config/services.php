<?php

use App\Framework\Container\ContainerInterface;
use App\Framework\Http\Request;
use App\Framework\Router\Router;
use App\Framework\Router\RouterInterface;

if (!isset($container) || !$container instanceof ContainerInterface) {
    throw new Exception('Configuration file needs $container object which implements ' . ContainerInterface::class);
}

// place service configuration calls here
$container
    ->bindInterface(RouterInterface::class, Router::class, 'getInstance')
    ->bindObject(Request::createFromGlobals())
;
