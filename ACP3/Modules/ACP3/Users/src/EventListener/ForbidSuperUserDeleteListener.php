<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\EventListener;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Model\Event\BeforeModelDeleteEvent;
use ACP3\Modules\ACP3\Users\Exception\SuperUserNotDeletableException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ForbidSuperUserDeleteListener implements EventSubscriberInterface
{
    public function __construct(private readonly UserModelInterface $userModel)
    {
    }

    /**
     * @throws SuperUserNotDeletableException
     */
    public function __invoke(BeforeModelDeleteEvent $event): void
    {
        foreach ($event->getEntryIdList() as $item) {
            $user = $this->userModel->getUserInfo((int) $item);
            if ($user['super_user'] == 1) {
                throw new SuperUserNotDeletableException();
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'users.model.users.before_delete' => '__invoke',
        ];
    }
}
