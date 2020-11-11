<?php

declare(strict_types=1);

use Simplex\Framework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

require dirname(__DIR__) . '/vendor/autoload.php';

$request = Request::createFromGlobals();
$requestContext = (new RequestContext())->fromRequest($request);
$routes = include dirname(__DIR__) . '/config/routes.php';
$urlMatcher = new UrlMatcher($routes, $requestContext);
$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();
$framework = new Framework($urlMatcher, $controllerResolver, $argumentResolver);

$response = $framework->handle($request);
$response->send();
