<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Application\Event\ControllerActionAfterDispatchEvent;
use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Controller\ActionInterface;
use ACP3\Core\Controller\Exception\ControllerActionNotFoundException;
use ACP3\Core\Http\RequestInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;

class ControllerActionDispatcher
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
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;
    /**
     * @var ArgumentResolverInterface
     */
    protected $argumentResolver;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \ACP3\Core\Http\RequestInterface                            $request
     * @param \Psr\Container\ContainerInterface                           $container
     * @param ArgumentResolverInterface                                   $argumentResolver
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        RequestInterface $request,
        ContainerInterface $container,
        ArgumentResolverInterface $argumentResolver
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->request = $request;
        $this->container = $container;
        $this->argumentResolver = $argumentResolver;
    }

    /**
     * @param string $serviceId
     * @param array  $arguments
     *
     * @return Response|string
     *
     * @throws ControllerActionNotFoundException
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \ReflectionException
     */
    public function dispatch(string $serviceId = '', array $arguments = [])
    {
        if (empty($serviceId)) {
            $serviceId = $this->buildControllerServiceId();
        }

        if ($this->container->has($serviceId)) {
            $this->eventDispatcher->dispatch(
                'core.application.controller_action_dispatcher.before_dispatch',
                new ControllerActionBeforeDispatchEvent($serviceId)
            );

            /** @var \ACP3\Core\Controller\ActionInterface $controller */
            $controller = $this->container->get($serviceId);
            $controller->preDispatch();
            $response = $controller->display($this->executeControllerAction($controller, $arguments));

            $this->eventDispatcher->dispatch(
                'core.application.controller_action_dispatcher.after_dispatch',
                new ControllerActionAfterDispatchEvent($serviceId, $response)
            );

            return $response;
        }

        throw new ControllerActionNotFoundException(
            'Service-Id ' . $serviceId . ' was not found!'
        );
    }

    /**
     * @return string
     */
    protected function buildControllerServiceId()
    {
        return $this->request->getModule()
            . '.controller.'
            . $this->request->getArea()
            . '.' . $this->request->getController()
            . '.' . $this->request->getAction();
    }

    /**
     * @param \ACP3\Core\Controller\ActionInterface $controller
     * @param array                                 $arguments
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    private function executeControllerAction(ActionInterface $controller, array $arguments)
    {
        $callable = $this->getCallable($controller);

        if (empty($arguments)) {
            $arguments = $this->argumentResolver->getArguments($this->request->getSymfonyRequest(), $callable);
        }

        return \call_user_func_array($callable, $arguments);
    }

    /**
     * @param ActionInterface $controller
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    private function getCallable(ActionInterface $controller)
    {
        $callable = [$controller, 'execute'];
        if (($this->request->getPost()->has('submit') || $this->request->getPost()->has('continue'))
            && \method_exists($controller, 'executePost')) {
            $reflection = new \ReflectionMethod($controller, 'executePost');

            if ($reflection->isPublic()) {
                $callable = [$controller, 'executePost'];
            }
        }

        return $callable;
    }
}
