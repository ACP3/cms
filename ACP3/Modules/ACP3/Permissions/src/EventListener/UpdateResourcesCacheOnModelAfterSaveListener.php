<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\EventListener;

use ACP3\Modules\ACP3\Permissions\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateResourcesCacheOnModelAfterSaveListener implements EventSubscriberInterface
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
        $this->cache->saveResourcesCache();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'permissions.model.acl_resources.after_save' => '__invoke',
            'permissions.model.acl_resources.after_delete' => '__invoke',
        ];
    }
}
