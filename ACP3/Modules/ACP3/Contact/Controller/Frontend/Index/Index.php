<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Contact\Validation\FormValidation
     */
    protected $formValidation;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var Contact\Model\ContactFormModel
     */
    protected $contactFormModel;
    /**
     * @var Contact\Model\ContactsModel
     */
    protected $contactsModel;
    /**
     * @var \ACP3\Core\Helpers\Alerts
     */
    private $alertsHelper;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext        $context
     * @param \ACP3\Core\Helpers\Forms                             $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                         $formTokenHelper
     * @param \ACP3\Core\Helpers\Alerts                            $alertsHelper
     * @param \ACP3\Modules\ACP3\Contact\Validation\FormValidation $formValidation
     * @param Contact\Model\ContactsModel                          $contactsModel
     * @param Contact\Model\ContactFormModel                       $contactFormModel
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Alerts $alertsHelper,
        Contact\Validation\FormValidation $formValidation,
        Contact\Model\ContactsModel $contactsModel,
        Contact\Model\ContactFormModel $contactFormModel
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->formValidation = $formValidation;
        $this->contactFormModel = $contactFormModel;
        $this->contactsModel = $contactsModel;
        $this->alertsHelper = $alertsHelper;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $copy = [
            1 => $this->translator->t('contact', 'send_copy_to_sender'),
        ];

        return [
            'form' => \array_merge($this->getFormDefaults(), $this->request->getPost()->all()),
            'copy' => $this->formsHelper->checkboxGenerator('copy', $copy, 0),
            'contact' => $this->config->getSettings(Contact\Installer\Schema::MODULE_NAME),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();
                $this->formValidation->validate($formData);

                $this->contactsModel->save($formData);

                $bool = $this->contactFormModel->sendContactFormEmail($formData);

                if (isset($formData['copy'])) {
                    $this->contactFormModel->sendContactFormEmailCopy($formData);
                }

                $this->setTemplate($this->alertsHelper->confirmBox(
                    $this->translator->t('contact', $bool === true ? 'send_mail_success' : 'send_mail_error'),
                    $this->router->route('contact')
                ));
            }
        );
    }

    /**
     * @return array
     */
    protected function getFormDefaults()
    {
        $defaults = [
            'name' => '',
            'name_disabled' => false,
            'mail' => '',
            'mail_disabled' => false,
            'message' => '',
        ];

        if ($this->user->isAuthenticated() === true) {
            $user = $this->user->getUserInfo();
            $defaults['name'] = !empty($user['realname']) ? $user['realname'] : $user['nickname'];
            $defaults['name_disabled'] = true;
            $defaults['mail'] = $user['mail'];
            $defaults['mail_disabled'] = true;
        }

        return $defaults;
    }
}
