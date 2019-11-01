<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules\Installer;

use ACP3\Core\Modules\SchemaHelper;

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
