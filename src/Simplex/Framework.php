<?php
declare(strict_types=1);

namespace Simplex;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class Framework
{

    /**
     * @var UrlMatcherInterface
     */
    private UrlMatcherInterface $urlMatcher;
    /**
     * @var ControllerResolverInterface
     */
    private ControllerResolverInterface $controllerResolver;
    /**
     * @var ArgumentResolverInterface
     */
    private ArgumentResolverInterface $argumentResolver;

    public function __construct(
        UrlMatcherInterface $urlMatcher,
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver
    ) {
        $this->urlMatcher = $urlMatcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
    }

    public function handle(Request $request)
    {
        $this->urlMatcher->getContext()->fromRequest($request);

        try {
            $request->attributes->add($this->urlMatcher->match($request->getPathInfo()));

            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);

            return call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $e) {
            return new Response('Not Found', 404);
        } catch (\Exception $e) {
            return new Response('An error occurred', 500);
        }
    }
}
