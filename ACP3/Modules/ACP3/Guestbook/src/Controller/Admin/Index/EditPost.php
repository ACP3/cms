<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Guestbook;

class EditPost extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
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
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Guestbook\Model\GuestbookModel $guestbookModel,
        Guestbook\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->guestbookModel = $guestbookModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id)
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
