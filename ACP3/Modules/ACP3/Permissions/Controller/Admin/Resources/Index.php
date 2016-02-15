<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\ResourceRepository
     */
    protected $resourceRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext              $context
     * @param \ACP3\Modules\ACP3\Permissions\Model\ResourceRepository $resourceRepository
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Permissions\Model\ResourceRepository $resourceRepository
    )
    {
        parent::__construct($context);

        $this->resourceRepository = $resourceRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $resources = $this->resourceRepository->getAllResources();
        $c_resources = count($resources);
        $output = [];
        for ($i = 0; $i < $c_resources; ++$i) {
            if ($this->modules->isActive($resources[$i]['module_name']) === true) {
                $module = $this->translator->t($resources[$i]['module_name'], $resources[$i]['module_name']);
                $output[$module][] = $resources[$i];
            }
        }
        ksort($output);

        return [
            'resources' => $output,
            'can_delete_resource' => $this->acl->hasPermission('admin/permissions/resources/delete'),
            'can_edit_resource' => $this->acl->hasPermission('admin/permissions/resources/edit')
        ];
    }
}
