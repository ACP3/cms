<?php
namespace ACP3\Core\Modules\Installer;

use ACP3\Core\Modules\SchemaHelper;

/**
 * Class InjectSchemaHelperTrait
 * @package ACP3\Core\Modules\Installer
 */
trait InjectSchemaHelperTrait
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