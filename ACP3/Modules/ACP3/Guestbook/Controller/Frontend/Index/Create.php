<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Guestbook;
use ACP3\Modules\ACP3\Newsletter;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Create extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Validation\FormValidation
     */
    protected $formValidation;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe|null
     */
    protected $newsletterSubscribeHelper;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var Guestbook\Model\GuestbookModel
     */
    protected $guestbookModel;
    /**
     * @var \ACP3\Core\Helpers\SendEmail
     */
    private $sendEmailHelper;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var array
     */
    private $guestbookSettings = [];

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\Router\RouterInterface $router,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\SendEmail $sendEmailHelper,
        Guestbook\Model\GuestbookModel $guestbookModel,
        Guestbook\Validation\FormValidation $formValidation,
        ?Newsletter\Helper\Subscribe $newsletterSubscribeHelper = null
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->formValidation = $formValidation;
        $this->guestbookModel = $guestbookModel;
        $this->sendEmailHelper = $sendEmailHelper;
        $this->newsletterSubscribeHelper = $newsletterSubscribeHelper;
        $this->router = $router;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->guestbookSettings = $this->config->getSettings(Guestbook\Installer\Schema::MODULE_NAME);
    }

    /**
     * @return array
     */
    public function execute()
    {
        if ($this->guestbookSettings['newsletter_integration'] == 1 && $this->newsletterSubscribeHelper) {
            $newsletterSubscription = [
                1 => $this->translator->t(
                    'guestbook',
                    'subscribe_to_newsletter',
                    ['%title%' => $this->config->getSettings(Schema::MODULE_NAME)['site_title']]
                ),
            ];
            $this->view->assign(
                'subscribe_newsletter',
                $this->formsHelper->checkboxGenerator('subscribe_newsletter', $newsletterSubscription, '1')
            );
        }

        return [
            'form' => \array_merge($this->fetchFormDefaults(), $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
            'can_use_emoticons' => $this->guestbookSettings['emoticons'] == 1,
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();
                $ipAddress = $this->request->getSymfonyRequest()->getClientIp();

                $this->formValidation
                    ->setIpAddress($ipAddress)
                    ->validate($formData);

                $formData['date'] = 'now';
                $formData['ip'] = $ipAddress;
                $formData['user_id'] = $this->user->isAuthenticated() ? $this->user->getUserId() : null;
                $formData['active'] = $this->guestbookSettings['notify'] == 2 ? 0 : 1;

                $lastId = $this->guestbookModel->save($formData);

                if ($this->guestbookSettings['notify'] == 1 || $this->guestbookSettings['notify'] == 2) {
                    $this->sendNotificationEmail($lastId);
                }

                return $this->redirectMessages()->setMessage(
                    $lastId,
                    $this->translator->t('system', $lastId !== false ? 'create_success' : 'create_error')
                );
            }
        );
    }

    /**
     * @param int $entryId
     */
    protected function sendNotificationEmail(int $entryId)
    {
        $fullPath = $this->router->route('guestbook', true) . '#gb-entry-' . $entryId;
        $body = \sprintf(
            $this->guestbookSettings['notify'] == 1
                ? $this->translator->t('guestbook', 'notification_email_body_1')
                : $this->translator->t('guestbook', 'notification_email_body_2'),
            $this->router->route('', true),
            $fullPath
        );
        $this->sendEmailHelper->execute(
            '',
            $this->guestbookSettings['notify_email'],
            $this->guestbookSettings['notify_email'],
            $this->translator->t('guestbook', 'notification_email_subject'),
            $body
        );
    }

    /**
     * @return array
     */
    private function fetchFormDefaults()
    {
        $defaults = [
            'name' => '',
            'name_disabled' => false,
            'mail' => '',
            'mail_disabled' => false,
            'website' => '',
            'website_disabled' => false,
            'message' => '',
        ];

        if ($this->user->isAuthenticated() === true) {
            $users = $this->user->getUserInfo();
            $defaults['name'] = $users['nickname'];
            $defaults['name_disabled'] = true;
            $defaults['mail'] = $users['mail'];
            $defaults['mail_disabled'] = true;
            $defaults['website'] = $users['website'];
            $defaults['website_disabled'] = !empty($users['website']);
        }

        return $defaults;
    }
}
