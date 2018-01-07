<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\View\Widget;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Contact\Installer\Schema;

class ContactSidebarBlock extends AbstractBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * ContactSidebarBlock constructor.
     * @param BlockContext $context
     * @param SettingsInterface $settings
     */
    public function __construct(BlockContext $context, SettingsInterface $settings)
    {
        parent::__construct($context);

        $this->settings = $settings;
    }

    /**
     * @inheritdoc
     */
    public function render(): array
    {
        return [
            'sidebar_contact' => $this->settings->getSettings(Schema::MODULE_NAME),
        ];
    }
}