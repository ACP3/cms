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
     * @var \ACP3\Modules\ACP3\Guestbook\Validation\FormValidation
     */
    private $formValidation;
    /**
     * @var Guestbook\Model\GuestbookModel
     */
    private $guestbookModel;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\ViewProviders\GuestbookCreateViewProvider
     */
    private $guestbookCreateViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Guestbook\Model\GuestbookModel $guestbookModel,
        Guestbook\Validation\FormValidation $formValidation,
        Guestbook\ViewProviders\GuestbookCreateViewProvider $guestbookCreateViewProvider
    ) {
        parent::__construct($context);

        $this->formValidation = $formValidation;
        $this->guestbookModel = $guestbookModel;
        $this->guestbookCreateViewProvider = $guestbookCreateViewProvider;
    }

    public function execute(): array
    {
        return ($this->guestbookCreateViewProvider)();
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

                return $this->redirectMessages()->setMessage(
                    $lastId,
                    $this->translator->t('system', $lastId !== false ? 'create_success' : 'create_error')
                );
            }
        );
    }
}
