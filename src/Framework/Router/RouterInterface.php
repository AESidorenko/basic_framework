<?php

namespace App\Framework\Router;

interface RouterInterface
{

    public function matchURL(string $url, string $method): ActionDescriptor;
}