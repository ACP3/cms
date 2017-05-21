<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Installer;

use ACP3\Core\Installer\Helper\InjectSchemaHelperTrait;

abstract class AbstractMigration implements MigrationInterface
{
    use InjectSchemaHelperTrait;
}
