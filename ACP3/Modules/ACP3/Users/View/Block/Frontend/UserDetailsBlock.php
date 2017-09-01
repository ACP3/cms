<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractBlock;

class UserDetailsBlock extends AbstractBlock
{

    /**
     * @inheritdoc
     */
    public function render()
    {
        $user = $this->getData();
        $user['gender'] = str_replace(
            [1, 2, 3],
            [
                '',
                $this->translator->t('users', 'female'),
                $this->translator->t('users', 'male')
            ],
            $user['gender']
        );

        return [
            'user' => $user
        ];
    }
}
