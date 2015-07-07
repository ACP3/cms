<?php

namespace ACP3\Modules\ACP3\Guestbook\Controller;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Emoticons;
use ACP3\Modules\ACP3\Guestbook;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Guestbook\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Model
     */
    protected $guestbookModel;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Validator
     */
    protected $guestbookValidator;
    /**
     * @var array
     */
    protected $guestbookSettings;
    /**
     * @var \ACP3\Modules\ACP3\Captcha\Helpers
     */
    protected $captchaHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    protected $emoticonsHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helpers
     */
    protected $newsletterHelpers;
    /**
     * @var bool
     */
    protected $emoticonsActive;
    /**
     * @var bool
     */
    protected $newsletterActive;

    /**
     * @param \ACP3\Core\Context\Frontend            $context
     * @param \ACP3\Core\Date                        $date
     * @param \ACP3\Core\Pagination                  $pagination
     * @param \ACP3\Core\Helpers\FormToken           $formTokenHelper
     * @param \ACP3\Modules\ACP3\Guestbook\Model     $guestbookModel
     * @param \ACP3\Modules\ACP3\Guestbook\Validator $guestbookValidator
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Core\Helpers\FormToken $formTokenHelper,
        Guestbook\Model $guestbookModel,
        Guestbook\Validator $guestbookValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->formTokenHelper = $formTokenHelper;
        $this->guestbookModel = $guestbookModel;
        $this->guestbookValidator = $guestbookValidator;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->guestbookSettings = $this->config->getSettings('guestbook');
        $this->emoticonsActive = ($this->guestbookSettings['emoticons'] == 1);
        $this->newsletterActive = ($this->guestbookSettings['newsletter_integration'] == 1);
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
     * @param \ACP3\Modules\ACP3\Emoticons\Helpers $emoticonsHelpers
     *
     * @return $this
     */
    public function setEmoticonsHelpers(Emoticons\Helpers $emoticonsHelpers)
    {
        $this->emoticonsHelpers = $emoticonsHelpers;

        return $this;
    }

    /**
     * @param \ACP3\Modules\ACP3\Newsletter\Helpers $newsletterHelpers
     *
     * @return $this
     */
    public function setNewsletterHelpers(Newsletter\Helpers $newsletterHelpers)
    {
        $this->newsletterHelpers = $newsletterHelpers;

        return $this;
    }

    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_createPost($this->request->getPost()->getAll());
        }

        // Emoticons einbinden
        if ($this->emoticonsActive === true && $this->emoticonsHelpers) {
            $this->view->assign('emoticons', $this->emoticonsHelpers->emoticonsList());
        }

        // In Newsletter integrieren
        if ($this->newsletterActive === true && $this->newsletterHelpers) {
            $this->view->assign('subscribe_newsletter', $this->get('core.helpers.forms')->selectEntry('subscribe_newsletter', '1', '1', 'checked'));
            $this->view->assign('LANG_subscribe_to_newsletter', sprintf($this->lang->t('guestbook', 'subscribe_to_newsletter'), $this->config->getSettings('seo')['title']));
        }

        $defaults = [
            'name' => '',
            'name_disabled' => '',
            'mail' => '',
            'mail_disabled' => '',
            'website' => '',
            'website_disabled' => '',
            'message' => '',
        ];

        // Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
        if ($this->auth->isUser() === true) {
            $users = $this->auth->getUserInfo();
            $disabled = ' readonly="readonly"';
            $defaults['name'] = $users['nickname'];
            $defaults['name_disabled'] = $disabled;
            $defaults['mail'] = $users['mail'];
            $defaults['mail_disabled'] = $disabled;
            $defaults['website'] = $users['website'];
            $defaults['website_disabled'] = !empty($users['website']) ? $disabled : '';
        }

        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->getAll()));

        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->captchaHelpers->captcha());
        }

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    public function actionIndex()
    {
        $this->view->assign('overlay', $this->guestbookSettings['overlay']);

        $guestbook = $this->guestbookModel->getAll($this->guestbookSettings['notify'], POS, $this->auth->entries);
        $c_guestbook = count($guestbook);

        if ($c_guestbook > 0) {
            $this->pagination->setTotalResults($this->guestbookModel->countAll($this->guestbookSettings['notify']));
            $this->pagination->display();

            for ($i = 0; $i < $c_guestbook; ++$i) {
                $guestbook[$i]['name'] = !empty($guestbook[$i]['user_name']) ? $guestbook[$i]['user_name'] : $guestbook[$i]['name'];
                if ($this->emoticonsActive === true && $this->emoticonsHelpers) {
                    $guestbook[$i]['message'] = $this->emoticonsHelpers->emoticonsReplace($guestbook[$i]['message']);
                }
                $guestbook[$i]['website'] = strlen($guestbook[$i]['user_website']) > 2 ? substr($guestbook[$i]['user_website'], 0, -2) : $guestbook[$i]['website'];
                if (!empty($guestbook[$i]['website']) && (bool)preg_match('=^http(s)?://=', $guestbook[$i]['website']) === false) {
                    $guestbook[$i]['website'] = 'http://' . $guestbook[$i]['website'];
                }

                $guestbook[$i]['mail'] = !empty($guestbook[$i]['user_mail']) ? substr($guestbook[$i]['user_mail'], 0, -2) : $guestbook[$i]['mail'];
            }
            $this->view->assign('guestbook', $guestbook);
            $this->view->assign('dateformat', $this->guestbookSettings['dateformat']);
        }
    }

    /**
     * @param array $formData
     */
    protected function _createPost(array $formData)
    {
        try {
            $this->guestbookValidator->validateCreate($formData, $this->newsletterActive);

            $insertValues = [
                'id' => '',
                'date' => $this->date->toSQL(),
                'ip' => $this->request->getServer()->get('REMOTE_ADDR', ''),
                'name' => Core\Functions::strEncode($formData['name']),
                'user_id' => $this->auth->isUser() ? $this->auth->getUserId() : '',
                'message' => Core\Functions::strEncode($formData['message']),
                'website' => Core\Functions::strEncode($formData['website']),
                'mail' => $formData['mail'],
                'active' => $this->guestbookSettings['notify'] == 2 ? 0 : 1,
            ];

            $lastId = $this->guestbookModel->insert($insertValues);

            // Send the notification E-mail if configured
            if ($this->guestbookSettings['notify'] == 1 || $this->guestbookSettings['notify'] == 2) {
                $fullPath = $this->router->route('guestbook', true) . '#gb-entry-' . $lastId;
                $body = sprintf(
                    $this->guestbookSettings['notify'] == 1 ? $this->lang->t('guestbook', 'notification_email_body_1') : $this->lang->t('guestbook', 'notification_email_body_2'),
                    $this->router->route('', true),
                    $fullPath
                );
                $this->get('core.helpers.sendEmail')->execute(
                    '',
                    $this->guestbookSettings['notify_email'],
                    $this->guestbookSettings['notify_email'],
                    $this->lang->t('guestbook', 'notification_email_subject'),
                    $body
                );
            }

            // Falls es der Benutzer ausgewählt hat, diesen in den Newsletter eintragen
            if ($this->newsletterActive === true &&
                $this->newsletterHelpers &&
                isset($formData['subscribe_newsletter']) &&
                $formData['subscribe_newsletter'] == 1
            ) {
                $this->newsletterHelpers->subscribeToNewsletter($formData['mail']);
            }

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
