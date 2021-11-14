<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\ViewProviders;

use ACP3\Core\ACL;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Permissions\Repository\AclResourceRepository;

class ResourceListDataGridViewProvider
{
    public function __construct(private ACL $acl, private Modules $modules, private AclResourceRepository $resourceRepository, private Translator $translator)
    {
    }

    public function __invoke(): array
    {
        $output = [];
        foreach ($this->resourceRepository->getAllResources() as $resource) {
            if ($this->modules->isInstalled($resource['module_name']) === true) {
                $module = $this->translator->t($resource['module_name'], $resource['module_name']);
                $output[$module][] = $resource;
            }
        }
        ksort($output);

        return [
            'resources' => $output,
            'can_delete_resource' => $this->acl->hasPermission('admin/permissions/resources/delete'),
            'can_edit_resource' => $this->acl->hasPermission('admin/permissions/resources/edit'),
        ];
    }
}
