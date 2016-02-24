<?php
namespace ACP3\Core\Application;

use ACP3\Core\Controller\ControllerInterface;
use ACP3\Core\Exceptions;
use ACP3\Core\Exceptions\ResultNotExists;
use ACP3\Core\Http\RequestInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FrontController
 * @package ACP3\Core\Application
 */
class FrontController
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $serviceId
     * @param array  $arguments
     * @param bool   $resolveArguments
     *
     * @throws \ACP3\Core\Exceptions\ControllerActionNotFound
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function dispatch($serviceId = '', array $arguments = [], $resolveArguments = true)
    {
        $request = $this->container->get('core.request');

        $this->checkForUriAlias($request);

        if (empty($serviceId)) {
            $serviceId = $request->getModule() . '.controller.' . $request->getArea() . '.' . $request->getController() . '.' . $request->getControllerAction();
        }

        if ($this->container->has($serviceId)) {
            /** @var \ACP3\Core\Controller\ControllerInterface $controller */
            $controller = $this->container->get($serviceId);
            $controller->setContainer($this->container);
            $controller->preDispatch();

            $result = $this->executeControllerAction(
                $request,
                $controller,
                'execute',
                $arguments,
                $resolveArguments
            );

            $controller->display($result);
            return;
        }

        throw new Exceptions\ControllerActionNotFound('Service-Id ' . $serviceId . ' was not found!');
    }

    /**
     * Checks, whether there is an URI alias available for the current request.
     * If so, set the alias as the canonical URI
     *
     * @param \ACP3\Core\Http\RequestInterface $request
     */
    protected function checkForUriAlias(RequestInterface $request)
    {
        // Return early, if we are currently in the admin panel
        if ($request->getArea() !== 'admin') {
            $routerAliases = $this->container->get('core.router.aliases');

            // If there is an URI alias available, set the alias as the canonical URI
            if ($routerAliases->uriAliasExists($request->getQuery()) === true &&
                $request->getOriginalQuery() !== $routerAliases->getUriAlias($request->getQuery()) . '/'
            ) {
                $this->container->get('core.seo')->setCanonicalUri(
                    $this->container->get('core.router')->route($request->getQuery())
                );
            }
        }
    }

    /**
     * @param \ACP3\Core\Http\RequestInterface          $request
     * @param \ACP3\Core\Controller\ControllerInterface $controller
     * @param string                                    $action
     * @param array                                     $arguments
     * @param bool                                      $resolveArguments
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    private function executeControllerAction(
        RequestInterface $request,
        ControllerInterface $controller,
        $action,
        array $arguments,
        $resolveArguments
    ) {
        $reflection = new \ReflectionMethod($controller, $action);
        $parameterCount = $reflection->getNumberOfParameters();

        if ($parameterCount > 0 && $resolveArguments === true) {
            $arguments = $this->fetchControllerActionArguments($request, $reflection);

            if ($reflection->getNumberOfRequiredParameters() > count($arguments)) {
                throw new ResultNotExists();
            }
        }

        return call_user_func_array([$controller, $action], $arguments);
    }

    /**
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ReflectionMethod                $reflection
     *
     * @return array
     */
    private function fetchControllerActionArguments(RequestInterface $request, \ReflectionMethod $reflection)
    {
        $arguments = [];
        foreach ($reflection->getParameters() as $parameter) {
            if ($request->getPost()->has($parameter->getName())) {
                $arguments[$parameter->getPosition()] = $request->getPost()->get($parameter->getName());
            } elseif ($request->getParameters()->has($parameter->getName())) {
                $arguments[$parameter->getPosition()] = $request->getParameters()->get($parameter->getName());
            } elseif ($parameter->isOptional()) {
                $arguments[$parameter->getPosition()] = $parameter->getDefaultValue();
            }
        }
        return $arguments;
    }
}
