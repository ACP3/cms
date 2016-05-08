<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources
 */
class Delete extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\ResourceRepository
     */
    protected $resourceRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext              $context
     * @param \ACP3\Modules\ACP3\Permissions\Model\ResourceRepository $resourceRepository
     * @param \ACP3\Modules\ACP3\Permissions\Cache                    $permissionsCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Permissions\Model\ResourceRepository $resourceRepository,
        Permissions\Cache $permissionsCache
    ) {
        parent::__construct($context);

        $this->resourceRepository = $resourceRepository;
        $this->permissionsCache = $permissionsCache;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;

                foreach ($items as $item) {
                    $bool = $this->resourceRepository->delete($item);
                }

                $this->permissionsCache->saveResourcesCache();

                return $bool;
            }
        );
    }
}
