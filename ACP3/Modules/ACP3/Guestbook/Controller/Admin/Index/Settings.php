<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Helpers\Secure;
use ACP3\Modules\ACP3\Guestbook;

class Settings extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Core\Helpers\Date
     */
    private $dateHelper;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Secure $secureHelper,
        Core\Helpers\Date $dateHelper,
        Guestbook\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->secureHelper = $secureHelper;
        $this->dateHelper = $dateHelper;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Guestbook\Installer\Schema::MODULE_NAME);

        $notificationTypes = [
            0 => $this->translator->t('guestbook', 'no_notification'),
            1 => $this->translator->t('guestbook', 'notify_on_new_entry'),
            2 => $this->translator->t('guestbook', 'notify_and_enable'),
        ];

        if ($this->modules->isActive('emoticons') === true) {
            $this->view->assign(
                'allow_emoticons',
                $this->formsHelper->yesNoCheckboxGenerator('emoticons', $settings['emoticons'])
            );
        }

        if ($this->modules->isActive('newsletter') === true) {
            $this->view->assign(
                'newsletter_integration',
                $this->formsHelper->yesNoCheckboxGenerator('newsletter_integration', $settings['newsletter_integration'])
            );
        }

        return [
            'dateformat' => $this->dateHelper->dateFormatDropdown($settings['dateformat']),
            'notify' => $this->formsHelper->choicesGenerator('notify', $notificationTypes, $settings['notify']),
            'overlay' => $this->formsHelper->yesNoCheckboxGenerator('overlay', $settings['overlay']),
            'form' => \array_merge(['notify_email' => $settings['notify_email']], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'dateformat' => $this->secureHelper->strEncode($formData['dateformat']),
                'notify' => $formData['notify'],
                'notify_email' => $formData['notify_email'],
                'overlay' => $formData['overlay'],
                'emoticons' => $formData['emoticons'],
                'newsletter_integration' => $formData['newsletter_integration'],
            ];

            return $this->config->saveSettings($data, Guestbook\Installer\Schema::MODULE_NAME);
        });
    }
}
