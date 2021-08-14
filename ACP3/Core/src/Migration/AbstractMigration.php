<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

use ACP3\Core\Migration\Exception\WrongMigrationNameException;

abstract class AbstractMigration implements MigrationInterface
{
    final public function getSchemaVersion(): int
    {
        $className = (new \ReflectionClass($this))->getShortName();

        if (!preg_match('/^Migration(\d+)$/', $className, $matches)) {
            throw new WrongMigrationNameException(sprintf('The migration file %s doesn\'t comply to the naming convention "Migration[0-9]+"!', __CLASS__));
        }

        return (int) $matches[1];
    }
}
