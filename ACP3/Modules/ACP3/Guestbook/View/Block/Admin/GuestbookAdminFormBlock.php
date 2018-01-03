<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\View\Block\Admin;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractAdminFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema;
use ACP3\Modules\ACP3\Guestbook\Model\Repository\GuestbookRepository;

class GuestbookAdminFormBlock extends AbstractAdminFormBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * GuestbookFormBlock constructor.
     * @param FormBlockContext $context
     * @param GuestbookRepository $repository
     * @param SettingsInterface $settings
     */
    public function __construct(
        FormBlockContext $context,
        GuestbookRepository $repository,
        SettingsInterface $settings
    ) {
        parent::__construct($context, $repository);

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
