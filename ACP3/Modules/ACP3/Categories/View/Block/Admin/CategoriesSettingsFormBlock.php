<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\View\Block\Admin;

use ACP3\Core\View\Block\AbstractSettingsFormBlock;
use ACP3\Modules\ACP3\Categories\Installer\Schema;

class CategoriesSettingsFormBlock extends AbstractSettingsFormBlock
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        return [
            'form' => \array_merge($this->getData(), $this->getRequestData()),
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
