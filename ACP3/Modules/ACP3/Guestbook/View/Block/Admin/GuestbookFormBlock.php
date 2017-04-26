<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\View\Block\Admin;


use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema;

class GuestbookFormBlock extends AbstractFormBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * GuestbookFormBlock constructor.
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
        $data = $this->getData();

        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $this->title->setPageTitlePrefix($data['name']);

        return [
            'form' => array_merge($data, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
            'can_use_emoticons' => $settings['emoticons'] == 1,
            'activate' => $settings['notify'] == 2
                ? $this->forms->yesNoCheckboxGenerator('active', $data['active'])
                : []
        ];

    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
