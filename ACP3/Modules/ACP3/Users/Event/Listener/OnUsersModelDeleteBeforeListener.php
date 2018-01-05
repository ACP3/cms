<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Users\Exception\SuperUserNotDeletableException;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class OnUsersModelDeleteBeforeListener
{
    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * OnUsersModelDeleteBeforeListener constructor.
     * @param UserModel $userModel
     */
    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * @param ModelSaveEvent $event
     * @throws SuperUserNotDeletableException
     */
    public function forbidSuperUserDelete(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $item) {
            $user = $this->userModel->getOneById($item);
            if ($user['super_user'] == 1) {
                throw new SuperUserNotDeletableException();
            }
        }
    }
}
