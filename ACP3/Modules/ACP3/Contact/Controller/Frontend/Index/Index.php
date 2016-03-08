<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Contact;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Contact\Controller\Frontend\Index
 */
class Index extends Core\Controller\FrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Core\Helpers\SendEmail
     */
    protected $sendEmailHelper;
    /**
     * @var \ACP3\Modules\ACP3\Contact\Validation\FormValidation
     */
    protected $formValidation;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext        $context
     * @param \ACP3\Core\Helpers\Forms                             $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                         $formTokenHelper
     * @param \ACP3\Core\Helpers\SendEmail                         $sendEmailHelper
     * @param \ACP3\Modules\ACP3\Contact\Validation\FormValidation $formValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\SendEmail $sendEmailHelper,
        Contact\Validation\FormValidation $formValidation
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->sendEmailHelper = $sendEmailHelper;
        $this->formValidation = $formValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->indexPost($this->request->getPost()->all());
        }

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

        return [
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'copy_checked' => $this->formsHelper->selectEntry('copy', 1, 0, 'checked'),
            'contact' => $this->config->getSettings('contact'),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function indexPost(array $formData)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $seoSettings = $this->config->getSettings('seo');
                $settings = $this->config->getSettings('contact');

                $this->formValidation->validate($formData);

                $formData['message'] = $this->get('core.helpers.secure')->strEncode($formData['message'], true);

                $subject = $this->translator->t('contact', 'contact_subject', ['%title%' => $seoSettings['title']]);
                $body = $this->translator->t(
                    'contact',
                    'contact_body',
                    [
                        '%name%' => $formData['name'],
                        '%mail%' => $formData['mail'],
                        '%message%' => $formData['message']
                    ]
                );
                $bool = $this->sendEmailHelper->execute('', $settings['mail'], $formData['mail'], $subject, $body);

                // Nachrichtenkopie an Absender senden
                if (isset($formData['copy'])) {
                    $subjectCopy = $this->translator->t('contact', 'sender_subject',
                        ['%title%' => $seoSettings['title']]);
                    $bodyCopy = $this->translator->t(
                        'contact',
                        'sender_body',
                        [
                            '%title%' => $seoSettings['title'],
                            '%message%' => $formData['message']
                        ]
                    );
                    $this->sendEmailHelper->execute($formData['name'], $formData['mail'], $settings['mail'], $subjectCopy, $bodyCopy);
                }

                $this->formTokenHelper->unsetFormToken();

                $this->setTemplate($this->get('core.helpers.alerts')->confirmBox(
                    $bool === true ? $this->translator->t('contact',
                        'send_mail_success') : $this->translator->t('contact', 'send_mail_error'),
                    $this->router->route('contact')
                ));
            }
        );
    }
}
