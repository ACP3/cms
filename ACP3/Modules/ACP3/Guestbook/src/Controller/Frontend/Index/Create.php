<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Guestbook;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Validation\FormValidation
     */
    private $formValidation;
    /**
     * @var Guestbook\Model\GuestbookModel
     */
    private $guestbookModel;
    /**
     * @var \ACP3\Core\Helpers\SendEmail
     */
    private $sendEmailHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\SendEmail $sendEmailHelper,
        Guestbook\Model\GuestbookModel $guestbookModel,
        Guestbook\Validation\FormValidation $formValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->formValidation = $formValidation;
        $this->guestbookModel = $guestbookModel;
        $this->sendEmailHelper = $sendEmailHelper;
    }

    public function execute(): array
    {
        return [
            'form' => \array_merge($this->fetchFormDefaults(), $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $guestbookSettings = $this->config->getSettings(Guestbook\Installer\Schema::MODULE_NAME);

                $formData = $this->request->getPost()->all();
                $ipAddress = $this->request->getSymfonyRequest()->getClientIp();

                $this->formValidation
                    ->setIpAddress($ipAddress)
                    ->validate($formData);

                $formData['date'] = 'now';
                $formData['ip'] = $ipAddress;
                $formData['user_id'] = $this->user->isAuthenticated() ? $this->user->getUserId() : null;
                $formData['active'] = $guestbookSettings['notify'] == 2 ? 0 : 1;

                $lastId = $this->guestbookModel->save($formData);

                if ($guestbookSettings['notify'] == 1 || $guestbookSettings['notify'] == 2) {
                    $this->sendNotificationEmail($lastId, $guestbookSettings);
                }

                return $this->redirectMessages()->setMessage(
                    $lastId,
                    $this->translator->t('system', $lastId !== false ? 'create_success' : 'create_error')
                );
            }
        );
    }

    protected function sendNotificationEmail(int $entryId, array $guestbookSettings): void
    {
        $fullPath = $this->router->route('guestbook', true) . '#gb-entry-' . $entryId;
        $body = \sprintf(
            $guestbookSettings['notify'] == 1
                ? $this->translator->t('guestbook', 'notification_email_body_1')
                : $this->translator->t('guestbook', 'notification_email_body_2'),
            $this->router->route('', true),
            $fullPath
        );
        $this->sendEmailHelper->execute(
            '',
            $guestbookSettings['notify_email'],
            $guestbookSettings['notify_email'],
            $this->translator->t('guestbook', 'notification_email_subject'),
            $body
        );
    }

    private function fetchFormDefaults(): array
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
