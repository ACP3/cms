<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Feeds\View\Block\Admin;

use ACP3\Core\View\Block\AbstractSettingsFormBlock;
use ACP3\Modules\ACP3\Feeds\Installer\Schema;

class FeedsSettingsFormBlock extends AbstractSettingsFormBlock
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        $settings = $this->getData();

        $feedTypes = [
            'RSS 1.0' => 'RSS 1.0',
            'RSS 2.0' => 'RSS 2.0',
            'ATOM' => 'ATOM'
        ];

        return [
            'feed_types' => $this->forms->choicesGenerator('feed_type', $feedTypes, $settings['feed_type']),
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
