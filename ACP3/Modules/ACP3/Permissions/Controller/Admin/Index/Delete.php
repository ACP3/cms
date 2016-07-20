<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Permissions\Model\Repository\RoleRepository;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Core\NestedSet\NestedSet
     */
    protected $nestedSet;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\NestedSet\NestedSet                       $nestedSet
     * @param \ACP3\Modules\ACP3\Permissions\Cache       $permissionsCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\NestedSet\NestedSet $nestedSet,
        Permissions\Cache $permissionsCache
    ) {
        parent::__construct($context);

        $this->nestedSet = $nestedSet;
        $this->permissionsCache = $permissionsCache;
    }

    /**
     * @param string $action
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $action, function ($items) {
            $bool = $levelNotDeletable = false;

            foreach ($items as $item) {
                if (in_array($item, [1, 2, 4]) === true) {
                    $levelNotDeletable = true;
                } else {
                    $bool = $this->nestedSet->deleteNode($item, RoleRepository::TABLE_NAME);
                }
            }

            $this->permissionsCache->getCacheDriver()->deleteAll();

            if ($levelNotDeletable === true) {
                $result = !$levelNotDeletable;
                $text = $this->translator->t('permissions', 'role_not_deletable');
            } else {
                $result = $bool !== false;
                $text = $this->translator->t('system', $result ? 'delete_success' : 'delete_error');
            }

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $this->redirectMessages()->setMessage($result, $text);
        }
        );
    }
}
