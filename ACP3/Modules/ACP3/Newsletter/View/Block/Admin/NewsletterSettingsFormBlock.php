<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\View\Block\Admin;

use ACP3\Core\View\Block\AbstractSettingsFormBlock;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema;

class NewsletterSettingsFormBlock extends AbstractSettingsFormBlock
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        $settings = $this->getData();

        return [
            'html' => $this->forms->yesNoCheckboxGenerator('html', $settings['html']),
            'form' => \array_merge($settings, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }
}
