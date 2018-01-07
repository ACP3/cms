<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer\Helper;

trait InjectSchemaHelperTrait
{
    /**
     * @var \ACP3\Core\Installer\Helper\SchemaHelper
     */
    protected $schemaHelper;

    /**
     * @param \ACP3\Core\Installer\Helper\SchemaHelper $schemaHelper
     */
    public function __construct(SchemaHelper $schemaHelper)
    {
        $this->schemaHelper = $schemaHelper;
    }
}