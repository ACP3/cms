<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty;

use ACP3\Core\Enum\BaseEnum;

class PluginTypeEnum extends BaseEnum
{
    public const BLOCK = 'block';
    public const FILTER = 'filter';
    public const FUNCTION = 'function';
    public const MODIFIER = 'modifier';
    public const RESOURCE = 'resource';
}
