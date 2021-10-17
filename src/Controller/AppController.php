<?php

namespace App\Controller;

use App\Framework\Controller\BasicController;
use App\Framework\Http\Request;
use App\Framework\Http\Response;

class AppController extends BasicController
{

    public function index(Request $request): Response
    {
        echo "Hello! " . $request->getMethod();

        return new Response();
    }
}