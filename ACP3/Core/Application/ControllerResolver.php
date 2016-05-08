<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Application\Event\FrontControllerDispatchEvent;
use ACP3\Core\Controller\ActionInterface;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Exceptions;
use ACP3\Core\Http\RequestInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ControllerResolver
 * @package ACP3\Core\Application
 */
class ControllerResolver
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \ACP3\Core\Http\RequestInterface                            $request
     * @param \Symfony\Component\DependencyInjection\ContainerInterface   $container
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        RequestInterface $request,
        ContainerInterface $container
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->request = $request;
        $this->container = $container;
    }

    /**
     * @param string $serviceId
     * @param array  $arguments
     *
     * @throws \ACP3\Core\Controller\Exception\ControllerActionNotFoundException
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function dispatch($serviceId = '', array $arguments = [])
    {
        if (empty($serviceId)) {
            $serviceId = $this->buildControllerServiceId();
        }

        if ($this->container->has($serviceId)) {
            $this->eventDispatcher->dispatch(
                'core.application.controller_resolver.before_dispatch',
                new FrontControllerDispatchEvent($serviceId)
            );

            /** @var \ACP3\Core\Controller\ActionInterface $controller */
            $controller = $this->container->get($serviceId);
            $controller->preDispatch();
            $controller->display($this->executeControllerAction($controller, $arguments));

            $this->eventDispatcher->dispatch(
                'core.application.controller_resolver.after_dispatch',
                new FrontControllerDispatchEvent($serviceId)
            );

            return;
        }

        throw new \ACP3\Core\Controller\Exception\ControllerActionNotFoundException('Service-Id ' . $serviceId . ' was not found!');
    }

    /**
     * @param \ACP3\Core\Controller\ActionInterface $controller
     * @param array                                 $arguments
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    private function executeControllerAction(ActionInterface $controller, array $arguments)
    {
        $reflection = new \ReflectionMethod($controller, 'execute');
        $parameterCount = $reflection->getNumberOfParameters();

        if ($parameterCount > 0 && empty($arguments)) {
            $arguments = $this->fetchControllerActionArguments($reflection);

            if ($reflection->getNumberOfRequiredParameters() > count($arguments)) {
                throw new ResultNotExistsException();
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
