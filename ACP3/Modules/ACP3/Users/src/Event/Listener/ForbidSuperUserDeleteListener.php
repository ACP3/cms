<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Event\Listener;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Users\Exception\SuperUserNotDeletableException;

class ForbidSuperUserDeleteListener
{
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $userModel;

    public function __construct(UserModelInterface $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * @throws SuperUserNotDeletableException
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $item) {
            $user = $this->userModel->getUserInfo($item);
            if ($user['super_user'] == 1) {
                throw new SuperUserNotDeletableException();
            }
        }
    }
}
