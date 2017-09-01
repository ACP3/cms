<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractFormBlock;

class UserForgotPasswordFormBlock extends AbstractFormBlock
{

    /**
     * @inheritdoc
     */
    public function render()
    {
        return [
            'form' => array_merge($this->getData(), $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken()
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return ['nick_mail' => ''];
    }
}
