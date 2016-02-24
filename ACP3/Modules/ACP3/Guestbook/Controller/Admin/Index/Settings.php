<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;
use ACP3\Modules\ACP3\Guestbook;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index
 */
class Settings extends Core\Controller\AdminAction
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
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                          $context
     * @param \ACP3\Core\Helpers\FormToken                                        $formTokenHelper
     * @param \ACP3\Modules\ACP3\Guestbook\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Guestbook\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    )
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('guestbook');

        $langNotify = [
            $this->translator->t('guestbook', 'no_notification'),
            $this->translator->t('guestbook', 'notify_on_new_entry'),
            $this->translator->t('guestbook', 'notify_and_enable')
        ];

        // Emoticons erlauben
        if ($this->modules->isActive('emoticons') === true) {
            $this->view->assign('allow_emoticons', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('emoticons', $settings['emoticons']));
        }

        // In Newsletter integrieren
        if ($this->modules->isActive('newsletter') === true) {
            $this->view->assign('newsletter_integration', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('newsletter_integration', $settings['newsletter_integration']));
        }

        return [
            'dateformat' => $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat']),
            'notify' => $this->get('core.helpers.forms')->selectGenerator('notify', [0, 1, 2], $langNotify, $settings['notify']),
            'overlay' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('overlay', $settings['overlay']),
            'form' => array_merge(['notify_email' => $settings['notify_email']], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'notify' => $formData['notify'],
                'notify_email' => $formData['notify_email'],
                'overlay' => $formData['overlay'],
                'emoticons' => $formData['emoticons'],
                'newsletter_integration' => $formData['newsletter_integration'],
            ];

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'guestbook');
        });
    }
}
