<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filescomments\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Comments\Helpers as CommentsHelpers;
use ACP3\Modules\ACP3\Files\Installer\Schema;

class OnFilesModelBeforeDeleteListener
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

    public function __invoke(ModelSaveEvent $event): void
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $item) {
            $this->commentsHelpers->deleteCommentsByModuleAndResult(
                $this->modules->getModuleId(Schema::MODULE_NAME),
                $item
            );
        }
    }
}
