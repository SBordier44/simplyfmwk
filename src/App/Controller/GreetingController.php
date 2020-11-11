<?php

declare(strict_types=1);

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GreetingController
{
    public function hello(Request $request, $name): Response
    {
        ob_start();
        include dirname(__DIR__) . '/View/hello.php';
        return new Response(ob_get_clean());
    }

    public function bye(): Response
    {
        ob_start();
        include dirname(__DIR__) . '/View/bye.php';
        return new Response(ob_get_clean());
    }
}
