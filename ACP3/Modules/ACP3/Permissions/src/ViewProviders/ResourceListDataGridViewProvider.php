<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\ViewProviders;

use ACP3\Core\ACL;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Permissions\Model\Repository\ResourceRepository;

class ResourceListDataGridViewProvider
{
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\ResourceRepository
     */
    private $resourceRepository;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        ACL $acl,
        Modules $modules,
        ResourceRepository $resourceRepository,
        Translator $translator
    ) {
        $this->acl = $acl;
        $this->modules = $modules;
        $this->resourceRepository = $resourceRepository;
        $this->translator = $translator;
    }

    public function __invoke()
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
