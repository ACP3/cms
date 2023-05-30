<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Services;

use ACP3\Modules\ACP3\Share\Model\ShareModel;
use ACP3\Modules\ACP3\Share\Validation\AdminFormValidation;

class ShareUpsertService
{
    public function __construct(private readonly ShareModel $shareModel, private readonly AdminFormValidation $adminFormValidation)
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
    public function upsert(array $updatedData, int $shareId = null): int
    {
        $shareInfo = $this->shareModel->getOneById($shareId);

        $this->adminFormValidation
            ->withUri($shareId !== null ? $shareInfo['uri'] : '')
            ->validate($updatedData);

        return $this->shareModel->save($updatedData, $shareId);
    }
}
