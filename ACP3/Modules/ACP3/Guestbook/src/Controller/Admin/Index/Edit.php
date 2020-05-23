<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Guestbook;

class Edit extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Validation\AdminFormValidation
     */
    private $adminFormValidation;
    /**
     * @var Guestbook\Model\GuestbookModel
     */
    private $guestbookModel;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\ViewProviders\AdminGuestbookEditViewProvider
     */
    private $adminGuestbookEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Guestbook\Model\GuestbookModel $guestbookModel,
        Guestbook\Validation\AdminFormValidation $adminFormValidation,
        Guestbook\ViewProviders\AdminGuestbookEditViewProvider $adminGuestbookEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->guestbookModel = $guestbookModel;
        $this->adminGuestbookEditViewProvider = $adminGuestbookEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): array
    {
        $guestbookEntry = $this->guestbookModel->getOneById($id);
        if (empty($guestbookEntry) === false) {
            return ($this->adminGuestbookEditViewProvider)($guestbookEntry);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $settings = $this->config->getSettings(Guestbook\Installer\Schema::MODULE_NAME);

            $this->adminFormValidation
                ->setSettings($settings)
                ->validate($formData);

            $formData['active'] = $settings['notify'] == 2 ? $formData['active'] : 1;

            return $this->guestbookModel->save($formData, $id);
        });
    }
}
