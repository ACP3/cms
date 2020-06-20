<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Users;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UsersModel
     */
    private $usersModel;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        Users\Model\UsersModel $usersModel
    ) {
        parent::__construct($context);

        $this->usersModel = $usersModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(?string $action = null)
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $action,
            function (array $items) {
                try {
                    $result = $this->usersModel->delete($items);
                    $text = $this->translator->t('system', $result !== false ? 'delete_success' : 'delete_error');
                } catch (Users\Exception\SuperUserNotDeletableException $e) {
                    $result = false;
                    $text = $this->translator->t('users', 'admin_user_undeletable');
                }

                return $this->actionHelper->setRedirectMessage(
                    $result,
                    $text
                );
            }
        );
    }
}
