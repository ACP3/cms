<?php
namespace ACP3\Core\Modules\Installer;

use ACP3\Core\Modules\SchemaHelper;

/**
 * Class AbstractMigration
 * @package ACP3\Core\Modules\Installer
 */
abstract class AbstractMigration implements MigrationInterface
{
    /**
     * @var \ACP3\Core\Modules\SchemaHelper
     */
    protected $schemaHelper;

    /**
     * @param \ACP3\Core\Modules\SchemaHelper $schemaHelper
     */
    public function __construct(SchemaHelper $schemaHelper)
    {
        $this->schemaHelper = $schemaHelper;
    }
}