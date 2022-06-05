<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\EventListener;

use ACP3\Modules\ACP3\System\Event\RenewCacheEvent;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RenewCachesListener implements EventSubscriberInterface
{
    public function __construct(private readonly CacheItemPoolInterface $coreCachePool, private readonly CacheItemPoolInterface $i18nCachePool)
    {
    }

    public function __invoke(): void
    {
        $this->coreCachePool->clear();
        $this->i18nCachePool->clear();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RenewCacheEvent::class => '__invoke',
        ];
    }
}
