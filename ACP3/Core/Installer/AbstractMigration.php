<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer;

use ACP3\Core\Installer\Helper\InjectSchemaHelperTrait;

abstract class AbstractMigration implements MigrationInterface
{
    use InjectSchemaHelperTrait;
}
