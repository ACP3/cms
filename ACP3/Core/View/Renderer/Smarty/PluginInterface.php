<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty;

interface PluginInterface
{
    const EXTENSION_TYPE_BLOCK = 'block';
    const EXTENSION_TYPE_FILTER = 'filter';
    const EXTENSION_TYPE_FUNCTION = 'function';
    const EXTENSION_TYPE_MODIFIER = 'modifier';
    const EXTENSION_TYPE_RESOURCE = 'resource';

    /**
     * @return string
     */
    public static function getExtensionType();

    /**
     * @return string
     */
    public static function getExtensionName();
}
