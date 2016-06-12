<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Application\Event\ControllerActionDispatcherDispatchEvent;
use ACP3\Core\Controller\ActionInterface;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;

/**
 * Class ControllerActionDispatcher
 * @package ACP3\Core\Application
 */
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
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var ArgumentResolverInterface
     */
    protected $argumentResolver;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param ArgumentResolverInterface $argumentResolver
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
     * @param array $arguments
     * @return Response|string
     *
     * @throws \ACP3\Core\Controller\Exception\ControllerActionNotFoundException
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function dispatch($serviceId = '', array $arguments = [])
    {
        if (empty($serviceId)) {
            $serviceId = $this->buildControllerServiceId();
        }

        if ($this->request->getArea() === AreaEnum::AREA_WIDGET &&
            $this->request->getServer()->get('REMOTE_ADDR') !== '127.0.0.1') {
            throw new \RuntimeException('Loading widgets from outside is not allowed!');
        }

        if ($this->container->has($serviceId)) {
            $this->eventDispatcher->dispatch(
                'core.application.controller_action_dispatcher.before_dispatch',
                new ControllerActionDispatcherDispatchEvent($serviceId)
            );

            /** @var \ACP3\Core\Controller\ActionInterface $controller */
            $controller = $this->container->get($serviceId);
            $controller->preDispatch();
            $response = $controller->display($this->executeControllerAction($controller, $arguments));

            $this->eventDispatcher->dispatch(
                'core.application.controller_action_dispatcher.after_dispatch',
                new ControllerActionDispatcherDispatchEvent($serviceId)
            );

            return $response;
        }

        throw new \ACP3\Core\Controller\Exception\ControllerActionNotFoundException(
            'Service-Id ' . $serviceId . ' was not found!'
        );
    }

    /**
     * @param \ACP3\Core\Controller\ActionInterface $controller
     * @param array $arguments
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    private function executeControllerAction(ActionInterface $controller, array $arguments)
    {
        $callable = [$controller, 'execute'];

        if (empty($arguments)) {
            $arguments = $this->argumentResolver->getArguments($this->request->getSymfonyRequest(), $callable);
        }

        return call_user_func_array($callable, $arguments);
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
}
