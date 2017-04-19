<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\View\Block\Admin;


use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Categories\Installer\Schema;

class CategoriesSettingsFormBlock extends AbstractFormBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * CategorySettingsFormBlock constructor.
     * @param FormBlockContext $context
     * @param SettingsInterface $settings
     */
    public function __construct(FormBlockContext $context, SettingsInterface $settings)
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
            'form' => array_merge($this->getData(), $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken()
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return $this->settings->getSettings(Schema::MODULE_NAME);
    }
}
