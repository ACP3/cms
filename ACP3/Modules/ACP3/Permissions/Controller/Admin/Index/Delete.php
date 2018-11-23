<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Delete extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;
    /**
     * @var Permissions\Model\RolesModel
     */
    protected $rolesModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Permissions\Model\RolesModel $rolesModel,
        Permissions\Cache $permissionsCache
    ) {
        parent::__construct($context);

        $this->permissionsCache = $permissionsCache;
        $this->rolesModel = $rolesModel;
    }

    /**
     * @param string $action
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(string $action = '')
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $action,
            function (array $items) {
                $bool = $levelNotDeletable = false;

                foreach ($items as $item) {
                    if (\in_array($item, [1, 2, 4]) === true) {
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

                return $this->redirectMessages()->setMessage($result, $text);
            }
        );
    }
}
