<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Cache\EventListener;

use ACP3\Core\Application\Event\ControllerActionAfterDispatchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CacheResponseListener implements EventSubscriberInterface
{
    public function __invoke(ControllerActionAfterDispatchEvent $event): void
    {
        $requestAttributes = $event->getRequest()->getSymfonyRequest()->attributes;

        if (!$requestAttributes->has('_acp3_http_cache_ttl')) {
            return;
        }

        $response = $event->getResponse();

        $varyHeaderName = 'X-User-Context-Hash';

        $response
            ->setVary($varyHeaderName)
            ->setSharedMaxAge(1)
            ->headers->add([
                $varyHeaderName => $event->getRequest()->getSymfonyRequest()->headers->get($varyHeaderName),
                'X-Reverse-Proxy-TTL' => $requestAttributes->get('_acp3_http_cache_ttl'),
            ]);

        $event->setResponse($response);
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
