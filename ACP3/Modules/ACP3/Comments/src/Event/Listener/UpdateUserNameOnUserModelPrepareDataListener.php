<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Event\Listener;

use ACP3\Core\Model\Event\ModelSavePrepareDataEvent;

class UpdateUserNameOnUserModelPrepareDataListener
{
    /**
     * This event listener checks whether a comment has been issued by a registered user.
     * If so, we ensure that the user ID and the user name can not be edited.
     */
    public function __invoke(ModelSavePrepareDataEvent $event): void
    {
        $currentData = $event->getCurrentData();

        if (!empty($currentData['user_id'])) {
            $event->replaceRawData('user_id', $currentData['user_id']);
            $event->replaceRawData('name', $currentData['name']);
        }
    }
}
