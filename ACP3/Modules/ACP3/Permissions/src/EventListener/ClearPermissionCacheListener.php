<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\EventListener;

use ACP3\Core\Cache;
use ACP3\Modules\ACP3\System\Event\RenewCacheEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ClearPermissionCacheListener implements EventSubscriberInterface
{
    /**
     * @var Cache
     */
    private $aclCache;

    public function __construct(Cache $aclCache)
    {
        $this->aclCache = $aclCache;
    }

    public function __invoke()
    {
        $this->aclCache->deleteAll();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'permissions.model.acl_resources.after_save' => '__invoke',
            'permissions.model.acl_resources.after_delete' => '__invoke',
            'permissions.model.acl_roles.after_save' => '__invoke',
            'permissions.model.acl_roles.after_delete' => '__invoke',
            RenewCacheEvent::class => '__invoke',
        ];
    }
}
