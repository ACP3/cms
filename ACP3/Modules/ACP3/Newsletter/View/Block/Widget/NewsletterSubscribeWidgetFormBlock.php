<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\View\Block\Widget;

use ACP3\Core\View\Block\AbstractFormBlock;

class NewsletterSubscribeWidgetFormBlock extends AbstractFormBlock
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        return [
            'form_token' => $this->formToken->renderFormToken()
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
