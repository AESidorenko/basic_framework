<?php

namespace App\Framework\Http;

use App\Framework\Base\Singleton;
use LogicException;

class Request extends Singleton
{
    private static array $server;
    private static array $get;
    private static array $post;
    private static array $request;
    private static array $env;
    private static array $cookie;
    private static array $session;
    private static array $files;

    public static function getInstance(): static
    {
        throw new LogicException('Basic instantiation not allowed');
    }
    
    static function createFromGlobals(): static
    {
        if (is_object(parent::$instance)) {
            throw new LogicException('Request object already created');
        }

        static::$server  = $_SERVER;
        static::$get     = $_GET;
        static::$post    = $_POST;
        static::$request = $_REQUEST;
        static::$env     = $_ENV;
        static::$cookie  = $_COOKIE;
        static::$session = $_SESSION ?? [];
        static::$files   = $_FILES;;

        return parent::getInstance();
    }

    public function getURL(): string
    {
        return explode('?', static::$server['REQUEST_URI'], 2)[0];
    }

    public function getMethod()
    {
        return static::$server['REQUEST_METHOD'];
    }
}