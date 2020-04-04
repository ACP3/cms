<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articlesmenus\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Articles\Helpers;
use ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem;

class OnArticlesModelAfterDeleteListener
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem
     */
    private $manageMenuItem;

    public function __construct(ManageMenuItem $manageMenuItem)
    {
        $this->manageMenuItem = $manageMenuItem;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $entryId) {
            $uri = \sprintf(Helpers::URL_KEY_PATTERN, $entryId);

            $this->manageMenuItem->manageMenuItem($uri);
        }
    }
}
