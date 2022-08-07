<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Users\Enum\GenderEnum;

class UserProfileViewProvider
{
    public function __construct(private readonly Translator $translator, private readonly UserModelInterface $userModel)
    {
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function __invoke(int $userId): array
    {
        $user = $this->userModel->getUserInfo($userId);
        $user['gender'] = str_replace(
            GenderEnum::values(),
            ['', $this->translator->t('users', 'female'), $this->translator->t('users', 'male')],
            (string) $user['gender']
        );

        return [
            'user' => $user,
        ];
    }
}
