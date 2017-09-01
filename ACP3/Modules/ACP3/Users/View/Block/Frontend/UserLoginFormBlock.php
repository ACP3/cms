<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractFormBlock;

class UserLoginFormBlock extends AbstractFormBlock
{

    /**
     * @inheritdoc
     */
    public function render()
    {
        $rememberMe = [
            1 => $this->translator->t('users', 'remember_me')
        ];

        return [
            'remember_me' => $this->forms->checkboxGenerator('remember', $rememberMe, 0)
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
