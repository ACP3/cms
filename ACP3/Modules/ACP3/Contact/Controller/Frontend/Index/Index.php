<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Contact\Controller\Frontend\Index
 */
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
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Contact\Validation\FormValidation $formValidation
     * @param Contact\Model\ContactFormModel $contactFormModel
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Contact\Validation\FormValidation $formValidation,
        Contact\Model\ContactFormModel $contactFormModel
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->formValidation = $formValidation;
        $this->contactFormModel = $contactFormModel;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->count() !== 0) {
            return $this->executePost($this->request->getPost()->all());
        }

        return [
            'form' => array_merge($this->getFormDefaults(), $this->request->getPost()->all()),
            'copy_checked' => $this->formsHelper->selectEntry('copy', 1, 0, 'checked'),
            'contact' => $this->config->getSettings(Contact\Installer\Schema::MODULE_NAME),
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
        return $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $this->formValidation->validate($formData);

                $bool = $this->contactFormModel->sendContactFormEmail($formData);

                if (isset($formData['copy'])) {
                    $this->contactFormModel->sendContactFormEmailCopy($formData);
                }

                $this->setTemplate($this->get('core.helpers.alerts')->confirmBox(
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
            'name_disabled' => '',
            'mail' => '',
            'mail_disabled' => '',
            'message' => '',
        ];

        // Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
        if ($this->user->isAuthenticated() === true) {
            $user = $this->user->getUserInfo();
            $disabled = ' readonly="readonly"';
            $defaults['name'] = !empty($user['realname']) ? $user['realname'] : $user['nickname'];
            $defaults['name_disabled'] = $disabled;
            $defaults['mail'] = $user['mail'];
            $defaults['mail_disabled'] = $disabled;
        }

        return $defaults;
    }
}
