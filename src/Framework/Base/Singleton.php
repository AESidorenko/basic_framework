<?php

namespace App\Framework\Base;

abstract class Singleton
{
    protected static array $instance = [];

    final protected function __construct()
    {
    }

    private function __clone()
    {
    }

    final public function __wakeup(): void
    {
    }

    public static function getInstance(): static
    {
        if (!isset(static::$instance[static::class])) {
            static::$instance[static::class] = new static();
        }

        return static::$instance[static::class];
    }
}