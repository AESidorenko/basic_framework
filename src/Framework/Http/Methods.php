<?php

namespace App\Framework\Http;

final class Methods
{
    public const HTTP_GET    = 'GET';
    public const HTTP_POST   = 'POST';
    public const HTTP_PATCH  = 'PATCH';
    public const HTTP_DELETE = 'DELETE';

    private function __construct()
    {
    }

    static function isMethodValid(string $method): bool
    {
        return in_array($method, [
            static::HTTP_GET,
            static::HTTP_POST,
            static::HTTP_PATCH,
            static::HTTP_DELETE,
        ]);
    }
}