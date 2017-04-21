<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\View\Frontend;


use ACP3\Core\I18n\Translator;
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
     * @var Translator
     */
    private $translator;

    /**
     * ImprintBlock constructor.
     * @param BlockContext $context
     * @param SettingsInterface $settings
     * @param Translator $translator
     */
    public function __construct(BlockContext $context, SettingsInterface $settings, Translator $translator)
    {
        parent::__construct($context);

        $this->settings = $settings;
        $this->translator = $translator;
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
