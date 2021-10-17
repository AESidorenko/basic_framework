<?php

use App\Controller\AppController;
use App\Framework\Router\RouterInterface;

if (!isset($router) || !$router instanceof RouterInterface) {
    throw new Exception('Configuration file needs $router object which implements ' . RouterInterface::class);
}

// place route configuration calls here
$router
    ->get('/', [AppController::class, 'index'])
;