<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallerycomments\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Comments\Helpers as CommentsHelpers;
use ACP3\Modules\ACP3\Comments\Installer\Schema as CommentsSchema;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;

class OnGalleryPictureModelBeforeDeleteListener
{
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var CommentsHelpers
     */
    private $commentsHelpers;

    public function __construct(
        Modules $modules,
        CommentsHelpers $commentsHelpers
    ) {
        $this->modules = $modules;
        $this->commentsHelpers = $commentsHelpers;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(ModelSaveEvent $event): void
    {
        if (!$this->modules->isInstalled(CommentsSchema::MODULE_NAME)) {
            return;
        }

        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $galleryPictureId) {
            $this->commentsHelpers->deleteCommentsByModuleAndResult(
                $this->modules->getModuleId(Schema::MODULE_NAME),
                $galleryPictureId
            );
        }
    }
}
