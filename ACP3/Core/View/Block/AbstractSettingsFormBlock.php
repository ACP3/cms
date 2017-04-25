<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block;


use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\Context\FormBlockContext;

abstract class AbstractSettingsFormBlock extends AbstractFormBlock implements SettingsFormBlockInterface
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    public function __construct(FormBlockContext $context, SettingsInterface $settings)
    {
        parent::__construct($context);

        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function getDefaultData(): array
    {
        return $this->settings->getSettings($this->getModuleName());
    }
}
