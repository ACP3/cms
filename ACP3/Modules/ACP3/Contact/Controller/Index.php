<?php

namespace ACP3\Modules\ACP3\Contact\Controller;

use ACP3\Core;
use ACP3\Core\Modules\FrontendController;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Contact;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Contact\Controller
 */
class Index extends Core\Modules\FrontendController
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
     * @var \ACP3\Modules\ACP3\Contact\Validator
     */
    protected $contactValidator;
    /**
     * @var \ACP3\Modules\ACP3\Captcha\Helpers
     */
    protected $captchaHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Core\Helpers\FormToken                  $formTokenHelper
     * @param \ACP3\Core\Helpers\SendEmail                  $sendEmailHelper
     * @param \ACP3\Modules\ACP3\Contact\Validator          $contactValidator
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\SendEmail $sendEmailHelper,
        Contact\Validator $contactValidator)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->sendEmailHelper = $sendEmailHelper;
        $this->contactValidator = $contactValidator;
    }

    /**
     * @param \ACP3\Modules\ACP3\Captcha\Helpers $captchaHelpers
     *
     * @return $this
     */
    public function setCaptchaHelpers(Captcha\Helpers $captchaHelpers)
    {
        $this->captchaHelpers = $captchaHelpers;

        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionIndex()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_indexPost($this->request->getPost()->all());
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

        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->all()));
        $this->view->assign('copy_checked', $this->get('core.helpers.forms')->selectEntry('copy', 1, 0, 'checked'));
        $this->view->assign('contact', $this->config->getSettings('contact'));

        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->captchaHelpers->captcha());
        }

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _indexPost(array $formData)
    {
        return $this->actionHelper->handlePostAction(
            function() use ($formData) {
                $seoSettings = $this->config->getSettings('seo');
                $settings = $this->config->getSettings('contact');

                $this->contactValidator->validate($formData);

                $formData['message'] = Core\Functions::strEncode($formData['message'], true);

                $subject = sprintf($this->lang->t('contact', 'contact_subject'), $seoSettings['title']);
                $body = str_replace(
                    ['{name}', '{mail}', '{message}', '\n'],
                    [$formData['name'], $formData['mail'], $formData['message'], "\n"],
                    $this->lang->t('contact', 'contact_body')
                );
                $bool = $this->sendEmailHelper->execute('', $settings['mail'], $formData['mail'], $subject, $body);

                // Nachrichtenkopie an Absender senden
                if (isset($formData['copy'])) {
                    $subjectCopy = sprintf($this->lang->t('contact', 'sender_subject'), $seoSettings['title']);
                    $bodyCopy = sprintf($this->lang->t('contact', 'sender_body'), $seoSettings['title'], $formData['message']);
                    $this->sendEmailHelper->execute($formData['name'], $formData['mail'], $settings['mail'], $subjectCopy, $bodyCopy);
                }

                $this->formTokenHelper->unsetFormToken();

                $this->setTemplate($this->get('core.helpers.alerts')->confirmBox(
                    $bool === true ? $this->lang->t('contact', 'send_mail_success') : $this->lang->t('contact', 'send_mail_error'),
                    $this->router->route('contact')
                ));
            }
        );
    }

    public function actionImprint()
    {
        $this->view->assign('imprint', $this->config->getSettings('contact'));
        $this->view->assign('powered_by', sprintf($this->lang->t('contact', 'powered_by'), '<a href="http://www.acp3-cms.net" target="_blank">ACP3</a>'));
    }
}
