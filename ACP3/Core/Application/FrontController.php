<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Controller\ActionInterface;
use ACP3\Core\Controller\AreaEnum;
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
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \ACP3\Core\Http\RequestInterface                          $request
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(
        RequestInterface $request,
        ContainerInterface $container
    ) {
        $this->request = $request;
        $this->container = $container;
    }

    /**
     * @param string $serviceId
     * @param array  $arguments
     *
     * @throws \ACP3\Core\Exceptions\ControllerActionNotFound
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function dispatch($serviceId = '', array $arguments = [])
    {
        $this->checkForUriAlias();

        if (empty($serviceId)) {
            $serviceId = $this->buildControllerServiceId();
        }

        if ($this->container->has($serviceId)) {
            /** @var \ACP3\Core\Controller\ActionInterface $controller */
            $controller = $this->container->get($serviceId);
            $controller->preDispatch();

            $result = $this->executeControllerAction($controller, $arguments);

            $controller->display($result);
            return;
        }

        throw new Exceptions\ControllerActionNotFound('Service-Id ' . $serviceId . ' was not found!');
    }

    /**
     * Checks, whether there is an URI alias available for the current request.
     * If so, set the alias as the canonical URI
     */
    protected function checkForUriAlias()
    {
        // Return early, if we are currently in the admin panel
        if ($this->request->getArea() !== AreaEnum::AREA_ADMIN) {
            $routerAliases = $this->container->get('core.router.aliases');

            // If there is an URI alias available, set the alias as the canonical URI
            if ($routerAliases->uriAliasExists($this->request->getQuery()) === true &&
                $this->request->getOriginalQuery() !== $routerAliases->getUriAlias($this->request->getQuery()) . '/'
            ) {
                $this->container->get('core.seo')->setCanonicalUri(
                    $this->container->get('core.router')->route($this->request->getQuery())
                );
            }
        }
    }

    /**
     * @param \ACP3\Core\Controller\ActionInterface $controller
     * @param array                                 $arguments
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    private function executeControllerAction(ActionInterface $controller, array $arguments)
    {
        $reflection = new \ReflectionMethod($controller, 'execute');
        $parameterCount = $reflection->getNumberOfParameters();

        if ($parameterCount > 0 && empty($arguments)) {
            $arguments = $this->fetchControllerActionArguments($reflection);

            if ($reflection->getNumberOfRequiredParameters() > count($arguments)) {
                throw new ResultNotExists();
            }
        }

        return call_user_func_array([$controller, 'execute'], $arguments);
    }

    /**
     * @param \ReflectionMethod $reflection
     *
     * @return array
     */
    private function fetchControllerActionArguments(\ReflectionMethod $reflection)
    {
        $arguments = [];
        foreach ($reflection->getParameters() as $parameter) {
            if ($this->request->getPost()->has($parameter->getName())) {
                $arguments[$parameter->getPosition()] = $this->request->getPost()->get($parameter->getName());
            } elseif ($this->request->getParameters()->has($parameter->getName())) {
                $arguments[$parameter->getPosition()] = $this->request->getParameters()->get($parameter->getName());
            } elseif ($parameter->isOptional()) {
                $arguments[$parameter->getPosition()] = $parameter->getDefaultValue();
            }
        }
        return $arguments;
    }

    /**
     * @return string
     */
    protected function buildControllerServiceId()
    {
        return $this->request->getModule() . '.controller.' . $this->request->getArea() . '.' . $this->request->getController() . '.' . $this->request->getAction();
    }
}
