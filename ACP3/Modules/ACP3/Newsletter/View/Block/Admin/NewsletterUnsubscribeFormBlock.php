<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\View\Block\Admin;

use ACP3\Core\View\Block\AbstractFormBlock;

class NewsletterUnsubscribeFormBlock extends AbstractFormBlock
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return [
            'form' => \array_merge($this->getData(), $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [
            'mail' => '',
        ];
    }
}
