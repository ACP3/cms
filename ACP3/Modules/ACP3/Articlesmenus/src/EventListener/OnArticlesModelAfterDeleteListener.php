<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articlesmenus\EventListener;

use ACP3\Core\Model\Event\AfterModelDeleteEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Articles\Helpers;
use ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem;
use ACP3\Modules\ACP3\Menus\Installer\Schema as MenusSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnArticlesModelAfterDeleteListener implements EventSubscriberInterface
{
    public function __construct(private readonly Modules $modules, private readonly ManageMenuItem $manageMenuItem)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(AfterModelDeleteEvent $event): void
    {
        if (!$this->modules->isInstalled(MenusSchema::MODULE_NAME)) {
            return;
        }

        foreach ($event->getEntryIdList() as $entryId) {
            $uri = sprintf(Helpers::URL_KEY_PATTERN, $entryId);

            $this->manageMenuItem->manageMenuItem($uri);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'articles.model.articles.after_delete' => '__invoke',
        ];
    }
}
