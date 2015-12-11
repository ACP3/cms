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
class Index extends Core\Modules\FrontendController
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
     * @var \ACP3\Modules\ACP3\Guestbook\Model\GuestbookRepository
     */
    protected $guestbookRepository;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Validation\FormValidation
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
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe
     */
    protected $newsletterSubscribeHelper;
    /**
     * @var bool
     */
    protected $emoticonsActive;
    /**
     * @var bool
     */
    protected $newsletterActive;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext          $context
     * @param \ACP3\Core\Date                                        $date
     * @param \ACP3\Core\Pagination                                  $pagination
     * @param \ACP3\Core\Helpers\FormToken                           $formTokenHelper
     * @param \ACP3\Modules\ACP3\Guestbook\Model\GuestbookRepository $guestbookRepository
     * @param \ACP3\Modules\ACP3\Guestbook\Validation\FormValidation $guestbookValidator
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Core\Helpers\FormToken $formTokenHelper,
        Guestbook\Model\GuestbookRepository $guestbookRepository,
        Guestbook\Validation\FormValidation $guestbookValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->formTokenHelper = $formTokenHelper;
        $this->guestbookRepository = $guestbookRepository;
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
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe $newsletterSubscribeHelper
     *
     * @return $this
     */
    public function setNewsletterSubscribeHelper(Newsletter\Helper\Subscribe $newsletterSubscribeHelper)
    {
        $this->newsletterSubscribeHelper = $newsletterSubscribeHelper;

        return $this;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_createPost($this->request->getPost()->all());
        }

        // Emoticons einbinden
        if ($this->emoticonsActive === true && $this->emoticonsHelpers) {
            $this->view->assign('emoticons', $this->emoticonsHelpers->emoticonsList());
        }

        // In Newsletter integrieren
        if ($this->newsletterActive === true && $this->newsletterSubscribeHelper) {
            $this->view->assign('subscribe_newsletter', $this->get('core.helpers.forms')->selectEntry('subscribe_newsletter', '1', '1', 'checked'));
            $this->view->assign(
                'LANG_subscribe_to_newsletter',
                $this->translator->t(
                    'guestbook',
                    'subscribe_to_newsletter',
                    ['%title%' => $this->config->getSettings('seo')['title']]
                )
            );
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
        if ($this->user->isAuthenticated() === true) {
            $users = $this->user->getUserInfo();
            $disabled = ' readonly="readonly"';
            $defaults['name'] = $users['nickname'];
            $defaults['name_disabled'] = $disabled;
            $defaults['mail'] = $users['mail'];
            $defaults['mail_disabled'] = $disabled;
            $defaults['website'] = $users['website'];
            $defaults['website_disabled'] = !empty($users['website']) ? $disabled : '';
        }

        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->captchaHelpers->captcha());
        }

        $this->formTokenHelper->generateFormToken();

        return [
            'form' => array_merge($defaults, $this->request->getPost()->all())
        ];
    }

    /**
     * @return array
     */
    public function actionIndex()
    {
        $guestbook = $this->guestbookRepository->getAll($this->guestbookSettings['notify'], POS, $this->user->getEntriesPerPage());
        $c_guestbook = count($guestbook);

        if ($c_guestbook > 0) {
            $this->pagination->setTotalResults($this->guestbookRepository->countAll($this->guestbookSettings['notify']));
            $this->pagination->display();

            for ($i = 0; $i < $c_guestbook; ++$i) {
                if ($this->emoticonsActive === true && $this->emoticonsHelpers) {
                    $guestbook[$i]['message'] = $this->emoticonsHelpers->emoticonsReplace($guestbook[$i]['message']);
                }
            }
            $this->view->assign('guestbook', $guestbook);
            $this->view->assign('dateformat', $this->guestbookSettings['dateformat']);
        }

        return [
            'overlay' => $this->guestbookSettings['overlay']
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _createPost(array $formData)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $this->guestbookValidator->validateCreate(
                    $formData,
                    $this->newsletterActive,
                    $this->request->getServer()->get('REMOTE_ADDR', '')
                );

                $insertValues = [
                    'id' => '',
                    'date' => $this->date->toSQL(),
                    'ip' => $this->request->getServer()->get('REMOTE_ADDR', ''),
                    'name' => Core\Functions::strEncode($formData['name']),
                    'user_id' => $this->user->isAuthenticated() ? $this->user->getUserId() : null,
                    'message' => Core\Functions::strEncode($formData['message']),
                    'website' => Core\Functions::strEncode($formData['website']),
                    'mail' => $formData['mail'],
                    'active' => $this->guestbookSettings['notify'] == 2 ? 0 : 1,
                ];

                $lastId = $this->guestbookRepository->insert($insertValues);

                // Send the notification E-mail if configured
                if ($this->guestbookSettings['notify'] == 1 || $this->guestbookSettings['notify'] == 2) {
                    $fullPath = $this->router->route('guestbook', true) . '#gb-entry-' . $lastId;
                    $body = sprintf(
                        $this->guestbookSettings['notify'] == 1 ? $this->translator->t('guestbook',
                            'notification_email_body_1') : $this->translator->t('guestbook',
                            'notification_email_body_2'),
                        $this->router->route('', true),
                        $fullPath
                    );
                    $this->get('core.helpers.sendEmail')->execute(
                        '',
                        $this->guestbookSettings['notify_email'],
                        $this->guestbookSettings['notify_email'],
                        $this->translator->t('guestbook', 'notification_email_subject'),
                        $body
                    );
                }

                // Falls es der Benutzer ausgewählt hat, diesen in den Newsletter eintragen
                if ($this->newsletterActive === true &&
                    $this->newsletterSubscribeHelper &&
                    isset($formData['subscribe_newsletter']) &&
                    $formData['subscribe_newsletter'] == 1
                ) {
                    $this->newsletterSubscribeHelper->subscribeToNewsletter($formData['mail']);
                }

                $this->formTokenHelper->unsetFormToken();

                return $this->redirectMessages()->setMessage($lastId,
                    $this->translator->t('system', $lastId !== false ? 'create_success' : 'create_error'));
            }
        );
    }
}
