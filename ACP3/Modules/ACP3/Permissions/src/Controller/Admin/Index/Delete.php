<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Permissions;

class Delete extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    private $permissionsCache;
    /**
     * @var Permissions\Model\RolesModel
     */
    private $rolesModel;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Permissions\Model\RolesModel $rolesModel,
        Permissions\Cache $permissionsCache
    ) {
        parent::__construct($context);

        $this->permissionsCache = $permissionsCache;
        $this->rolesModel = $rolesModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function execute(?string $action = null)
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

                $this->permissionsCache->getCacheDriver()->deleteAll();

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
