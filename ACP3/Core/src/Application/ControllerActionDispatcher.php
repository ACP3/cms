<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Application\Event\ControllerActionAfterDispatchEvent;
use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Application\Event\ControllerActionRequestEvent;
use ACP3\Core\Controller\Exception\ControllerActionNotFoundException;
use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Core\Http\RequestInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;

class ControllerActionDispatcher
{
    private const POST_SERVICE_ID_SUFFIX = '_post';

    public function __construct(private EventDispatcherInterface $eventDispatcher, private RequestInterface $request, private ContainerInterface $serviceLocator, private ArgumentResolverInterface $argumentResolver)
    {
    }

    /**
     * @param array<string, mixed> $arguments
     *
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

            if (substr($serviceId, $suffixLengthOffset) === self::POST_SERVICE_ID_SUFFIX) {
                $normalizedServiceId = substr($serviceId, 0, $suffixLengthOffset);
            }
            $this->eventDispatcher->dispatch(
                new ControllerActionBeforeDispatchEvent($normalizedServiceId),
                ControllerActionBeforeDispatchEvent::class
            );

            /** @var \ACP3\Core\Controller\InvokableActionInterface $controller */
            $controller = $this->serviceLocator->get($serviceId);
            $controller->preDispatch();

            $response = $this->executeControllerAction($controller, $arguments);

            if (!($response instanceof Response)) {
                $response = $controller->display($response);
            }

            $afterDispatchEvent = new ControllerActionAfterDispatchEvent($normalizedServiceId, $this->request, $response);
            $this->eventDispatcher->dispatch(
                $afterDispatchEvent,
                ControllerActionAfterDispatchEvent::class
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

    /**
     * @param array<string, mixed> $arguments
     */
    private function modifyRequest(string $serviceId, array $arguments): void
    {
        [$module, , , $controller, $action] = explode('.', $serviceId);

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
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>|Response|string|null
     *
     * @throws \ACP3\Core\Controller\Exception\ControllerActionNotFoundException
     * @throws \ReflectionException
     */
    private function executeControllerAction(InvokableActionInterface $action, array $arguments): array|Response|string|null
    {
        if (empty($arguments)) {
            try {
                $arguments = $this->argumentResolver->getArguments($this->request->getSymfonyRequest(), $action);
            } catch (\RuntimeException $e) {
                throw new ControllerActionNotFoundException($e->getMessage(), $e);
            }
        }

        return $action(...$arguments);
    }
}
