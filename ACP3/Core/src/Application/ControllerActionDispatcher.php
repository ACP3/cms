<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Application\Event\ControllerActionAfterDispatchEvent;
use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Application\Event\ControllerActionRequestEvent;
use ACP3\Core\Controller\ActionInterface;
use ACP3\Core\Controller\Exception\ControllerActionNotFoundException;
use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Core\Http\RequestInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;

class ControllerActionDispatcher
{
    private const ACTION_METHOD_INVOKABLE = '__invoke';
    private const ACTION_METHOD_DEFAULT = 'execute';
    private const ACTION_METHOD_POST = 'executePost';
    private const POST_SERVICE_ID_SUFFIX = '_post';

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $serviceLocator;
    /**
     * @var ArgumentResolverInterface
     */
    private $argumentResolver;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        RequestInterface $request,
        ContainerInterface $serviceLocator,
        ArgumentResolverInterface $argumentResolver
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->request = $request;
        $this->serviceLocator = $serviceLocator;
        $this->argumentResolver = $argumentResolver;
    }

    /**
     * @throws ControllerActionNotFoundException
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \ReflectionException
     */
    public function dispatch(?string $serviceId = null, array $arguments = []): Response
    {
        $controllerActionRequestEvent = new ControllerActionRequestEvent($this->request);
        $this->eventDispatcher->dispatch(
            $controllerActionRequestEvent,
            ControllerActionRequestEvent::NAME
        );

        if ($controllerActionRequestEvent->hasResponse()) {
            return $controllerActionRequestEvent->getResponse();
        }

        if (empty($serviceId)) {
            $serviceId = $this->buildControllerServiceId();
        } else {
            $this->modifyRequest($serviceId, $arguments);
        }

        if ($this->shouldUsePostAction($serviceId)) {
            $serviceId .= self::POST_SERVICE_ID_SUFFIX;
        }

        if ($this->serviceLocator->has($serviceId)) {
            $normalizedServiceId = $serviceId;

            $suffixLengthOffset = \strlen(self::POST_SERVICE_ID_SUFFIX) * -1;

            if (\substr($serviceId, $suffixLengthOffset) === self::POST_SERVICE_ID_SUFFIX) {
                $normalizedServiceId = \substr($serviceId, 0, $suffixLengthOffset);
            }
            $this->eventDispatcher->dispatch(
                new ControllerActionBeforeDispatchEvent($normalizedServiceId),
                ControllerActionBeforeDispatchEvent::NAME
            );

            /** @var \ACP3\Core\Controller\ActionInterface $controller */
            $controller = $this->serviceLocator->get($serviceId);
            $controller->preDispatch();
            $response = $controller->display($this->executeControllerAction($controller, $arguments));

            $afterDispatchEvent = new ControllerActionAfterDispatchEvent($normalizedServiceId, $this->request, $response);
            $this->eventDispatcher->dispatch(
                $afterDispatchEvent,
                ControllerActionAfterDispatchEvent::NAME
            );

            return $afterDispatchEvent->getResponse();
        }

        throw new ControllerActionNotFoundException('Service-Id ' . $serviceId . ' was not found!');
    }

    private function buildControllerServiceId(): string
    {
        return $this->request->getModule()
            . '.controller.'
            . $this->request->getArea()
            . '.' . $this->request->getController()
            . '.' . $this->request->getAction();
    }

    private function modifyRequest(string $serviceId, array $arguments): void
    {
        [$module, , , $controller, $action] = \explode('.', $serviceId);

        $params = '';
        foreach ($arguments as $key => $value) {
            $params .= "/{$key}_{$value}";
        }

        $this->request->setPathInfo($module . '/' . $controller . '/' . $action . $params);
        $this->request->processQuery();
    }

    private function shouldUsePostAction(?string $serviceId): bool
    {
        return $this->serviceLocator->has($serviceId . self::POST_SERVICE_ID_SUFFIX)
            && $this->request->getSymfonyRequest()->isMethod('POST')
            && ($this->request->getPost()->has('submit') || $this->request->getPost()->has('continue'));
    }

    /**
     * @return mixed
     *
     * @throws \ACP3\Core\Controller\Exception\ControllerActionNotFoundException
     * @throws \ReflectionException
     */
    private function executeControllerAction(ActionInterface $controller, array $arguments)
    {
        $callable = $this->getCallable($controller);

        if (empty($arguments)) {
            try {
                $arguments = $this->argumentResolver->getArguments($this->request->getSymfonyRequest(), $callable);
            } catch (\RuntimeException $e) {
                throw new ControllerActionNotFoundException($e->getMessage(), $e);
            }
        }

        return \call_user_func_array($callable, $arguments);
    }

    /**
     * @throws \ReflectionException
     * @throws \ACP3\Core\Controller\Exception\ControllerActionNotFoundException
     */
    private function getCallable(ActionInterface $controller): array
    {
        if ($controller instanceof InvokableActionInterface && \method_exists($controller, self::ACTION_METHOD_INVOKABLE) === true) {
            return [$controller, self::ACTION_METHOD_INVOKABLE];
        }

        if ($this->isValidPostRequest($controller)) {
            $reflection = new \ReflectionMethod($controller, self::ACTION_METHOD_POST);

            if ($reflection->isPublic()) {
                return [$controller, self::ACTION_METHOD_POST];
            }
        }

        if (\method_exists($controller, self::ACTION_METHOD_DEFAULT) === true) {
            return [$controller, self::ACTION_METHOD_DEFAULT];
        }

        throw new ControllerActionNotFoundException(\sprintf('Could not find method <%s> in controller <%s>', self::ACTION_METHOD_DEFAULT, \get_class($controller)));
    }

    private function isValidPostRequest(ActionInterface $controller): bool
    {
        if (\method_exists($controller, self::ACTION_METHOD_POST)
            && $this->request->getSymfonyRequest()->isMethod('POST')) {
            return $this->request->getPost()->has('submit')
                || $this->request->getPost()->has('continue')
                || \method_exists($controller, self::ACTION_METHOD_DEFAULT) === false;
        }

        return false;
    }
}
