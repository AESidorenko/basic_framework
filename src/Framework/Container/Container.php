<?php

namespace App\Framework\Container;

use App\Framework\Base\Singleton;

class Container extends Singleton implements ContainerInterface
{
    private static array $interfaces = [];
    private static array $classes    = [];
    private static array $builtIns   = [];

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function instantiate(string $className, string $staticMethod = ''): object
    {
        if (interface_exists($className)) {
            if (!array_key_exists($className, static::$interfaces)) {
                throw new \Exception(sprintf('Interface %s not found in the container', $className)); // todo: make exception class
            }

            $className = static::$interfaces[$className];
        }

        $classReflection = new \ReflectionClass($className);
        if (array_key_exists($className, static::$classes)) {
            if (static::$classes[$className] instanceof $className) {
                return static::$classes[$className];
            }

            $constructorReflection = static::$classes[$className];
        } else {
            $constructorReflection = empty($staticMethod) ? $classReflection->getConstructor() : $classReflection->getMethod($staticMethod);
        }

        $constructorArguments = $constructorReflection ? $constructorReflection->getParameters() : [];

        $parameters = $this->prepareParameters($constructorArguments);

        if ($constructorReflection) {
            $object = $constructorReflection->isConstructor() ? $classReflection->newInstanceArgs($parameters) : $constructorReflection->invokeArgs(null, $parameters);
        } else {
            $object = $classReflection->newInstanceWithoutConstructor();
        }

        static::$classes[$className] = $object;

        return $object;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function addService(string $className, string $staticMethod): void
    {
        if (!class_exists($className)) {
            throw new \Exception(sprintf('Unknown interface %s', $className)); // todo: introduce custom exception class
        }

        if (array_key_exists($className, static::$classes)) {
            throw new \Exception(sprintf('Class %s already exists in the container', $className)); // todo: introduce custom exception class
        }

        static::$classes[$className] = new \ReflectionMethod($className, $staticMethod);
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function bindInterface(string $interfaceName, string $className, string $staticMethod = ''): self
    {
        if (!interface_exists($interfaceName)) {
            throw new \Exception(sprintf('Unknown interface %s', $interfaceName)); // todo: introduce custom exception class
        }

        $classReflection = new \ReflectionClass($className);

        if (!$classReflection->isSubclassOf($interfaceName)) {
            throw new \Exception(sprintf('Class %s doesn\'t implement interface %s', $className, $interfaceName)); // todo: introduce custom exception class
        }

        if (array_key_exists($interfaceName, static::$interfaces)) {
            throw new \Exception(sprintf('Interface %s already defined in the container', $interfaceName)); // todo: introduce custom exception class
        }

        static::$interfaces[$interfaceName] = $className;

        if (!empty($staticMethod)) {
            $this->addService($className, $staticMethod);
        }

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function bindObject(object $object, string $interfaceName = ''): self
    {
        $className = $object::class;
        if (empty($interfaceName)) {
            if (array_key_exists($className, static::$classes) && static::$classes[$className] instanceof $className) {
                throw new \Exception(sprintf('Class %s already defined in the container', $className)); // todo: introduce custom exception class
            }

            static::$classes[$className] = $object;

            return $this;
        }

        if (!interface_exists($interfaceName)) {
            throw new \Exception(sprintf('Unknown interface %s', $interfaceName)); // todo: introduce custom exception class
        }

        if (array_key_exists($interfaceName, static::$interfaces) && static::$interfaces[$interfaceName] !== $className) {
            throw new \Exception(sprintf('Interface implementation class %s differs from object\'s class', static::$interfaces[$interfaceName])); // todo: introduce custom exception class
        }

        if (!$className instanceof $interfaceName) {
            throw new \Exception(sprintf('Object\'s class %s doesn\'t implement interface %s', $className, $interfaceName)); // todo: introduce custom exception class
        }

        if (!array_key_exists($interfaceName, static::$interfaces)) {
            static::$interfaces[$interfaceName] = $className;
        }

        static::$classes[$className] = $object;

        return $this;
    }

    public function findService(string $className): object
    {
        if (array_key_exists($className, static::$interfaces)) {
            $className = static::$interfaces[$className];
        }

        if (!array_key_exists($className, static::$classes)) {
            throw new \Exception(sprintf('Class %s not found in the container', $className)); // todo: introduce custom exception class
        }

        if (!static::$classes[$className] instanceof $className) {
            throw new \Exception(sprintf('Class %s hasn\'t been instantiated yet', $className)); // todo: introduce custom exception class
        }

        return static::$classes[$className];
    }

    /**
     * @throws \ReflectionException
     */
    public function prepareToCall(string $className, string $methodName): array
    {
        $object = $this->instantiate($className);

        $arguments = (new \ReflectionMethod($object, $methodName))->getParameters();

        return [$object, $this->prepareParameters($arguments)];
    }

    /**
     * @param array $arguments
     * @return array
     * @throws \ReflectionException
     */
    private function prepareParameters(array $arguments): array
    {
        $parameters = [];
        foreach ($arguments as $argument) {
            $type = $argument->getType();
            $name = $argument->getName();

            if ($type->isBuiltin()) {
                $builtInParamId = implode(' ', [$type->getName(), $name]);
                if (!array_key_exists($builtInParamId, static::$builtIns)) {
                    throw new \Exception(sprintf('Parameter %s not found in the container', $builtInParamId)); // todo: introduce custom exception class
                }

                $parameters[$name] = static::$builtIns[$builtInParamId];

                continue;
            }

            $typename = $type->getName();

            $parameters[$name] = array_key_exists($typename, static::$classes) ? static::$classes[$typename] : static::instantiate($typename);
        }

        return $parameters;
    }
}