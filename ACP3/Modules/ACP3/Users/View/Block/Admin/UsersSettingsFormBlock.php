<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\View\Block\Admin;

use ACP3\Core\View\Block\AbstractSettingsFormBlock;
use ACP3\Modules\ACP3\Users\Installer\Schema;

class UsersSettingsFormBlock extends AbstractSettingsFormBlock
{

    /**
     * @inheritdoc
     */
    public function render()
    {
        $settings = $this->getData();

        return [
            'registration' => $this->forms->yesNoCheckboxGenerator(
                'enable_registration',
                $settings['enable_registration']
            ),
            'form' => array_merge($settings, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken()
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
