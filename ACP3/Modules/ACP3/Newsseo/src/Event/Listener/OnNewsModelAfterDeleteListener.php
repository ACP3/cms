<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsseo\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\News\Helpers;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

class OnNewsModelAfterDeleteListener
{
    /**
     * @var UriAliasManager
     */
    private $uriAliasManager;

    /**
     * OnNewsModelAfterDeleteListener constructor.
     */
    public function __construct(UriAliasManager $uriAliasManager)
    {
        $this->uriAliasManager = $uriAliasManager;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $item) {
            $uri = \sprintf(Helpers::URL_KEY_PATTERN, $item);
            $this->uriAliasManager->deleteUriAlias($uri);
        }
    }
}