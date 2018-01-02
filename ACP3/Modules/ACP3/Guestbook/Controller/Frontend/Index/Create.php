<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Guestbook;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Validation\FormValidation
     */
    protected $formValidation;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe
     */
    protected $newsletterSubscribeHelper;
    /**
     * @var Guestbook\Model\GuestbookModel
     */
    protected $guestbookModel;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;
    /**
     * @var Core\Helpers\SendEmail
     */
    private $sendEmail;
    /**
     * @var array
     */
    protected $guestbookSettings = [];

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param Guestbook\Model\GuestbookModel $guestbookModel
     * @param \ACP3\Modules\ACP3\Guestbook\Validation\FormValidation $formValidation
     * @param Core\Helpers\SendEmail $sendEmail
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Guestbook\Model\GuestbookModel $guestbookModel,
        Guestbook\Validation\FormValidation $formValidation,
        Core\Helpers\SendEmail $sendEmail
    ) {
        parent::__construct($context);

        $this->formValidation = $formValidation;
        $this->guestbookModel = $guestbookModel;
        $this->block = $block;
        $this->sendEmail = $sendEmail;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->guestbookSettings = $this->config->getSettings(Schema::MODULE_NAME);
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
     * @return array
     */
    public function execute()
    {
        return $this->block
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();
                $ipAddress = $this->request->getSymfonyRequest()->getClientIp();

                $this->formValidation
                    ->setIpAddress($ipAddress)
                    ->setNewsletterAccess($this->guestbookSettings['newsletter_integration'] == 1)
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
    protected function sendNotificationEmail($entryId)
    {
        $fullPath = $this->router->route('guestbook', true) . '#gb-entry-' . $entryId;
        $body = sprintf(
            $this->guestbookSettings['notify'] == 1
                ? $this->translator->t('guestbook', 'notification_email_body_1')
                : $this->translator->t('guestbook', 'notification_email_body_2'),
            $this->router->route('', true),
            $fullPath
        );

        $message = (new Core\Mailer\MailerMessage())
            ->setSubject($this->translator->t('guestbook', 'notification_email_subject'))
            ->setBody($body)
            ->setFrom($this->guestbookSettings['notify_email'])
            ->setRecipients($this->guestbookSettings['notify_email']);
        $this->sendEmail->execute($message);
    }
}
