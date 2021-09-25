<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Permissions;

class Delete extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var Permissions\Model\AclRoleModel
     */
    private $rolesModel;
    /**
     * @var \ACP3\Core\Helpers\FormAction
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        FormAction $actionHelper,
        Permissions\Model\AclRoleModel $rolesModel
    ) {
        parent::__construct($context);

        $this->rolesModel = $rolesModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(?string $action = null)
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $action,
            function (array $items) {
                $bool = $levelNotDeletable = false;

                foreach ($items as $item) {
                    if (\in_array((int) $item, [1, 2, 4], true) === true) {
                        $levelNotDeletable = true;
                    } else {
                        $bool = $this->rolesModel->delete($item);
                    }
                }

                if ($levelNotDeletable === true) {
                    $result = false;
                    $text = $this->translator->t('permissions', 'role_not_deletable');
                } else {
                    $result = $bool !== false;
                    $text = $this->translator->t('system', $result ? 'delete_success' : 'delete_error');
                }

                return $this->actionHelper->setRedirectMessage($result, $text);
            }
        );
    }
}
