<?php

namespace App\Framework\Router;

use App\Framework\Base\Singleton;
use App\Framework\Http\Methods;

class Router extends Singleton implements RouterInterface
{
    private static array $actionUrls = [];

    private static function getActionKey(string $url, string $method): string
    {
        return sprintf('%s:%s', $url, $method);
    }

    /**
     * @throws \Exception
     */
    private static function addActionUrl(string $url, string $method, array $action): void
    {
        $actionKey = static::getActionKey($url, $method);
        if (array_key_exists($actionKey, static::$actionUrls)) {
            throw new \Exception('Action url already defined'); // todo: introduce custom exception class
        }

        if (!Methods::isMethodValid($method)) {
            throw new \Exception('Invalid HTTP method'); // todo: introduce custom exception class
        }

        static::$actionUrls[$actionKey] = $action;
    }

    /**
     * @throws \Exception
     */
    public function get(string $url, array $action): self
    {
        static::addActionUrl($url, Methods::HTTP_GET, $action);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function post(string $url, array $action): self
    {
        static::addActionUrl($url, Methods::HTTP_POST, $action);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function patch(string $url, array $action): self
    {
        static::addActionUrl($url, Methods::HTTP_PATCH, $action);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function delete(string $url, array $action): self
    {
        static::addActionUrl($url, Methods::HTTP_DELETE, $action);

        return $this;
    }

    public function matchURL(string $url, string $method): ActionDescriptor
    {
        $actionKey = static::getActionKey($url, $method);

        if (!array_key_exists($actionKey, static::$actionUrls)) {
            throw new \Exception('Url not found exception'); // todo: introduce custom exception class
        }

        return ActionDescriptor::fromArray(static::$actionUrls[$actionKey]);
    }
}