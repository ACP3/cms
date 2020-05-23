<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\I18n\Translator;

class UserProfileViewProvider
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $userModel;

    public function __construct(Translator $translator, UserModelInterface $userModel)
    {
        $this->translator = $translator;
        $this->userModel = $userModel;
    }

    public function __invoke(int $userId): array
    {
        $user = $this->userModel->getUserInfo($userId);
        $user['gender'] = \str_replace(
            [1, 2, 3],
            ['', $this->translator->t('users', 'female'), $this->translator->t('users', 'male')],
            $user['gender']
        );

        return [
            'user' => $user,
        ];
    }
}
