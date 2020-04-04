<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Galleryshare\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Gallery\Helpers as GalleryHelpers;
use ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager;

class OnGalleryPictureModelBeforeDeleteListener
{
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager
     */
    private $socialSharingManager;

    public function __construct(SocialSharingManager $socialSharingManager)
    {
        $this->socialSharingManager = $socialSharingManager;
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
            $uri = \sprintf(GalleryHelpers::URL_KEY_PATTERN_PICTURE, $item);

            $this->socialSharingManager->saveSharingInfo($uri);
        }
    }
}
