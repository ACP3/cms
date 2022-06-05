<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\EventListener;

use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\Event\ModelSavePrepareDataEvent;
use ACP3\Modules\ACP3\Users\Model\UserModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HashPasswordOnUserModelPrepareData implements EventSubscriberInterface
{
    public function __construct(private readonly Secure $secure)
    {
    }

    public function __invoke(ModelSavePrepareDataEvent $event): void
    {
        $updatedData = $event->getRawData();
        $currentData = $event->getCurrentData();

        if ($this->needsNewPassword($updatedData, $currentData)) {
            $salt = $this->secure->salt(UserModel::SALT_LENGTH);
            $event->replaceRawData('pwd_salt', $salt);
            $event->replaceRawData('pwd', $this->hashPassword($salt, $updatedData['pwd']));
        }
    }

    /**
     * @param array<string, mixed>      $updatedData
     * @param array<string, mixed>|null $currentData
     */
    private function needsNewPassword(array $updatedData, ?array $currentData): bool
    {
        if (!$currentData) {
            return true;
        }

        $currentSalt = $currentData['pwd_salt'];

        return $updatedData['pwd'] !== $currentData['pwd']
            && $this->hashPassword($currentSalt, $updatedData['pwd']) !== $currentData['pwd'];
    }

    private function hashPassword(string $salt, string $plaintextPassword): string
    {
        return $this->secure->generateSaltedPassword($salt, $plaintextPassword, 'sha512');
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'users.model.users.prepare_data' => '__invoke',
        ];
    }
}
