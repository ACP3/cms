<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Repository;

use ACP3\Core\Repository\RepositoryInterface;

class AbstractStubRepository implements RepositoryInterface
{
    public function insert(array $data): int
    {
        return 0;
    }

    public function getTableName(string $tableName = ''): string
    {
        return $tableName;
    }

    public function delete(array|int|string $entryId, ?string $columnName = null): int
    {
        return 0;
    }

    public function update(array $data, array|int|string $entryId): int
    {
        return 0;
    }

    public function getOneById(int|string $entryId): array
    {
        return [];
    }
}
