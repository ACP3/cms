<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty;

enum PluginTypeEnum: string
{
    case BLOCK = 'block';
    case FILTER = 'filter';
    case FUNCTION = 'function';
    case MODIFIER = 'modifier';
    case RESOURCE = 'resource';
}
