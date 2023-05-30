<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\EventListener;

use ACP3\Core\Application\Event\OutputPageExceptionEvent;
use ACP3\Core\Controller\Exception\ForwardControllerActionAwareExceptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ForwardControllerActionExceptionErrorListener implements EventSubscriberInterface
{
    public function __construct(private readonly HttpKernelInterface $kernel)
    {
    }

    public function __invoke(OutputPageExceptionEvent $event): void
    {
        // Return early, if the event has already been handled by another exception listener
        if ($event->hasResponse()) {
            return;
        }

        $throwable = $event->getThrowable();

        if ($throwable instanceof ForwardControllerActionAwareExceptionInterface) {
            $subRequest = Request::create($this->convertForwardParametersToRequestUri($throwable->getServiceId(), $throwable->routeParams()));
            $event->setResponse(
                $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST),
            );
        }
    }

    /**
     * @param array<string, string> $routeParams
     */
    private function convertForwardParametersToRequestUri(string $serviceId, array $routeParams): string
    {
        [$module, , $area, $controller, $action] = explode('.', $serviceId);

        $requestUri = $this->convertAreaFromServiceId($area) . '/' . $module . '/' . $controller . '/' . $action . '/';

        foreach ($routeParams as $key => $value) {
            $requestUri .= $key . '_' . $value . '/';
        }

        return $requestUri;
    }

    private function convertAreaFromServiceId(string $serviceIdArea): string
    {
        return match ($serviceIdArea) {
            'admin' => '/acp',
            'widget' => '/widget',
            default => '',
        };
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OutputPageExceptionEvent::NAME => ['__invoke', -1024],
        ];
    }
}
