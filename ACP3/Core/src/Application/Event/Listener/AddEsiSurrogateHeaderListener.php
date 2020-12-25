<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event\Listener;

use ACP3\Core\Application\Event\ControllerActionAfterDispatchEvent;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AddEsiSurrogateHeaderListener implements EventSubscriberInterface
{
    public function __invoke(ControllerActionAfterDispatchEvent $event)
    {
        $response = $event->getResponse();

        if ($this->isExcludedFromEsi($event->getRequest(), $response)) {
            return;
        }

        $response->headers->set('Surrogate-Control', 'content="ESI/1.0"');

        $event->setResponse($response);
    }

    private function isExcludedFromEsi(RequestInterface $request, Response $response): bool
    {
        return $request->getArea() === AreaEnum::AREA_WIDGET
            || $response instanceof BinaryFileResponse
            || $response instanceof StreamedResponse;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ControllerActionAfterDispatchEvent::NAME => '__invoke',
        ];
    }
}
