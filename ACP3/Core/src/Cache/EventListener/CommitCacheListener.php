<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Cache\EventListener;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class CommitCacheListener implements EventSubscriberInterface
{
    /**
     * @var ServiceLocator
     */
    private $cacheItemPoolsServiceLocator;

    public function __construct(ServiceLocator $cacheItemPoolsServiceLocator)
    {
        $this->cacheItemPoolsServiceLocator = $cacheItemPoolsServiceLocator;
    }

    public function __invoke(): void
    {
        foreach ($this->cacheItemPoolsServiceLocator->getProvidedServices() as $serviceId => $class) {
            /** @var CacheItemPoolInterface $cacheItemPool */
            $cacheItemPool = $this->cacheItemPoolsServiceLocator->get($serviceId);
            $cacheItemPool->commit();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TerminateEvent::class => '__invoke',
        ];
    }
}
