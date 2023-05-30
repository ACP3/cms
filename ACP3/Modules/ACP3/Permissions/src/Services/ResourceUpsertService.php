<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Services;

use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Permissions\Model\AclResourceModel;
use ACP3\Modules\ACP3\Permissions\Validation\ResourceFormValidation;

class ResourceUpsertService
{
    public function __construct(private readonly Modules $modules, private readonly AclResourceModel $aclResourceModel, private readonly ResourceFormValidation $resourceFormValidation)
    {
    }

    /**
     * @param array<string, mixed> $updatedData
     *
     * @throws \ACP3\Core\Validation\Exceptions\InvalidFormTokenException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function upsert(array $updatedData, int $aclResourceId = null): int
    {
        $this->resourceFormValidation->validate($updatedData);

        $updatedData['module_id'] = $this->modules->getModuleInfo($updatedData['modules'])['id'] ?? 0;

        return $this->aclResourceModel->save($updatedData, $aclResourceId);
    }
}
