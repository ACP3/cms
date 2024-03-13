<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Repository;

interface RepositoryInterface
{
    /**
     * Executes the SQL insert statement.
     *
     * The method will return the last inserted ID.
     *
     * @param array<string, mixed> $data
     */
    public function insert(array $data): int;

    public function getTableName(string $tableName = ''): string;

    /**
     * Executes the SQL delete statement.
     *
     * @param int|string|array<string, int|string> $entryId
     */
    public function delete(array|int|string $entryId, ?string $columnName = null): int;

    /**
     * Executes the SQL update statement.
     *
     * @param array<string, mixed>                 $data
     * @param array<string, string|int>|int|string $entryId
     */
    public function update(array $data, array|int|string $entryId): int;

    /**
     * Returns a single full result set by the value of its primary key.
     *
     * @return array<string, mixed>
     */
    public function getOneById(int|string $entryId): array;
}
