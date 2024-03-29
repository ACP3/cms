<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filescomments\EventListener;

use ACP3\Core\Model\Event\BeforeModelDeleteEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Comments\Helpers as CommentsHelpers;
use ACP3\Modules\ACP3\Comments\Installer\Schema as CommentsSchema;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnFilesModelBeforeDeleteListener implements EventSubscriberInterface
{
    public function __construct(private readonly Modules $modules, private readonly CommentsHelpers $commentsHelpers)
    {
    }

    public function __invoke(BeforeModelDeleteEvent $event): void
    {
        if (!$this->modules->isInstalled(CommentsSchema::MODULE_NAME)) {
            return;
        }

        foreach ($event->getEntryIdList() as $item) {
            $this->commentsHelpers->deleteCommentsByModuleAndResult(
                $this->modules->getModuleId(Schema::MODULE_NAME),
                $item
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'files.model.files.before_delete' => '__invoke',
        ];
    }
}
