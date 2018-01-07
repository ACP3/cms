<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\View\Block\Widget;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Users\Installer\Schema;

class UserLoginWidgetFormBlock extends AbstractFormBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * UserLoginWidgetFormBlock constructor.
     *
     * @param FormBlockContext  $context
     * @param SettingsInterface $settings
     */
    public function __construct(FormBlockContext $context, SettingsInterface $settings)
    {
        parent::__construct($context);

        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        return [
            'enable_registration' => $settings['enable_registration'],
            'redirect_uri' => $this->getData()['redirect_url'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
