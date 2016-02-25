<?php
namespace ACP3\Core\Modules\Installer;

/**
 * Class AbstractMigration
 * @package ACP3\Core\Modules\Installer
 */
abstract class AbstractMigration implements MigrationInterface
{
    use InjectSchemaHelperTrait;
}
