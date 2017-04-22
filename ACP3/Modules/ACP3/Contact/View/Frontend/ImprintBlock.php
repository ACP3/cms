<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\View\Frontend;


use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Contact\Installer\Schema;

class ImprintBlock extends AbstractBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * ImprintBlock constructor.
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
    public function render()
    {
        return [
            'imprint' => $this->settings->getSettings(Schema::MODULE_NAME),
            'powered_by' => $this->translator->t(
                'contact',
                'powered_by',
                [
                    '%ACP3%' => '<a href="https://www.acp3-cms.net" target="_blank">ACP3 CMS</a>'
                ]
            )
        ];
    }
}
