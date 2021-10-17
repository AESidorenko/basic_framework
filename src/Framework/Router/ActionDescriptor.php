<?php

namespace App\Framework\Router;

final class ActionDescriptor
{
    private string $className;
    private string $methodName;

    public static function fromArray(array $descriptorArray): ActionDescriptor
    {
        return new ActionDescriptor(...$descriptorArray);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @param string $className
     * @param string $methodName
     */
    public function __construct(string $className, string $methodName)
    {
        $this->className  = $className;
        $this->methodName = $methodName;
    }
}