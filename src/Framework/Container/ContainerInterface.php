<?php

namespace App\Framework\Container;

interface ContainerInterface
{
    function bindInterface(string $interfaceName, string $className, string $staticMethod = ''): self;
    function bindObject(object $object, string $interfaceName = ''): self;
    function addService(string $className, string $staticMethod): void;

    function findService(string $className): object;
    function instantiate(string $className): object;

    public function prepareToCall(string $getClassName, string $getMethodName): array;
}