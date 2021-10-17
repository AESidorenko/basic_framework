<?php

namespace App\Framework\Application;

use App\Framework\Base\Singleton;
use App\Framework\Container\ContainerInterface;
use App\Framework\Http\Request;
use App\Framework\Router\RouterInterface;

class Application extends Singleton
{
    private ContainerInterface $container;
    private RouterInterface    $router;
    private Request            $request;

    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }

    private function init(): void
    {
        $this->router  = $this->container->findService(RouterInterface::class);
        $this->request = $this->container->findService(Request::class);
    }

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $this->init();

        // todo: move matchURL into Application class
        $actionDescriptor = $this->router->matchURL($this->request->getURL(), $this->request->getMethod());

        [$controllerObject, $parameters] = $this->container->prepareToCall($actionDescriptor->getClassName(), $actionDescriptor->getMethodName());

        call_user_func_array([$controllerObject, $actionDescriptor->getMethodName()], $parameters);
    }
}