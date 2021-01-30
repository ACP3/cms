<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\EventListener;

use ACP3\Modules\ACP3\Seo\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateSeoCacheOnModelAfterSaveListener implements EventSubscriberInterface
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function __invoke()
    {
        $this->cache->saveCache();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'seo.model.seo.after_save' => '__invoke',
            'seo.model.seo.after_delete' => '__invoke',
        ];
    }
}
