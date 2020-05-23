<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\ResourceRepository
     */
    protected $resourceRepository;
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\ACL $acl,
        Core\Modules $modules,
        Permissions\Model\Repository\ResourceRepository $resourceRepository
    ) {
        parent::__construct($context);

        $this->resourceRepository = $resourceRepository;
        $this->acl = $acl;
        $this->modules = $modules;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $resources = $this->resourceRepository->getAllResources();
        $output = [];
        foreach ($resources as $resource) {
            if ($this->modules->isActive($resource['module_name']) === true) {
                $module = $this->translator->t($resource['module_name'], $resource['module_name']);
                $output[$module][] = $resource;
            }
        }
        \ksort($output);

        return [
            'resources' => $output,
            'can_delete_resource' => $this->acl->hasPermission('admin/permissions/resources/delete'),
            'can_edit_resource' => $this->acl->hasPermission('admin/permissions/resources/edit'),
        ];
    }
}
