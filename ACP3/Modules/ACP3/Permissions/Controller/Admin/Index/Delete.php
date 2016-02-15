<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin\Index
 */
class Delete extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\NestedSet
     */
    protected $nestedSet;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\NestedSet                       $nestedSet
     * @param \ACP3\Modules\ACP3\Permissions\Cache       $permissionsCache
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\NestedSet $nestedSet,
        Permissions\Cache $permissionsCache
    )
    {
        parent::__construct($context);

        $this->nestedSet = $nestedSet;
        $this->permissionsCache = $permissionsCache;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = $levelNotDeletable = false;

                foreach ($items as $item) {
                    if (in_array($item, [1, 2, 4]) === true) {
                        $levelNotDeletable = true;
                    } else {
                        $bool = $this->nestedSet->deleteNode($item, Permissions\Model\RoleRepository::TABLE_NAME);
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

                return $this->redirectMessages()->setMessage($result, $text);
            }
        );
    }
}
