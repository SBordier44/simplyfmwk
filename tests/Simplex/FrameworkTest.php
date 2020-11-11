<?php

declare(strict_types=1);

namespace Tests\Simplex;


use PHPUnit\Framework\TestCase;
use RuntimeException;
use Simplex\Framework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;

class FrameworkTest extends TestCase
{
    public function testNotFoundHandling(): void
    {
        $framework = $this->getFrameworkForException(new ResourceNotFoundException());
        $response = $framework->handle(new Request());
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testErrorHandling(): void
    {
        $framework = $this->getFrameworkForException(new RuntimeException());
        $response = $framework->handle(new Request());
        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testHelloMethodFromGreetingControllerReturnValidResponse(): void
    {
        $matcher = $this->createMock(UrlMatcherInterface::class);
        $matcher
            ->expects(self::once())
            ->method('match')
            ->willReturn(
                [
                    '_route' => 'hello/{name}',
                    'name' => 'Sylvain',
                    '_controller' => 'App\Controller\GreetingController::hello'
                ]
            );
        $matcher
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($this->createMock(RequestContext::class));
        $controllerResolver = new ControllerResolver();
        $argumentsResolver = new ArgumentResolver();
        $framework = new Framework($matcher, $controllerResolver, $argumentsResolver);
        $response = $framework->handle(new Request());
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertStringContainsString('Hello Sylvain', $response->getContent());
    }

    public function testByeMethodFromGreetingControllerReturnValidResponse(): void
    {
        $matcher = $this->createMock(UrlMatcherInterface::class);
        $matcher
            ->expects(self::once())
            ->method('match')
            ->willReturn(
                [
                    '_route' => 'bye',
                    '_controller' => 'App\Controller\GreetingController::bye'
                ]
            );
        $matcher
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($this->createMock(RequestContext::class));
        $controllerResolver = new ControllerResolver();
        $argumentsResolver = new ArgumentResolver();
        $framework = new Framework($matcher, $controllerResolver, $argumentsResolver);
        $response = $framework->handle(new Request());
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertStringContainsString('Goodbye!', $response->getContent());
    }

    private function getFrameworkForException(RuntimeException $exception): Framework
    {
        $matcher = $this->createMock(UrlMatcherInterface::class);
        $matcher
            ->expects(self::once())
            ->method('match')
            ->will(self::throwException($exception));
        $matcher
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($this->createMock(RequestContext::class));
        $controllerResolver = $this->createMock(ControllerResolverInterface::class);
        $argumentsResolver = $this->createMock(ArgumentResolverInterface::class);

        return new Framework($matcher, $controllerResolver, $argumentsResolver);
    }
}
