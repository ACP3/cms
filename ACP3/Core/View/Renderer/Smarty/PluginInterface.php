<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty;

interface PluginInterface
{
    /**
     * @deprecated since version 4.30.0, to be removed with 5.0.0. Use the ACP3\Core\View\Renderer\Smarty\PluginTypeEnum instead
     */
    const EXTENSION_TYPE_BLOCK = 'block';
    /**
     * @deprecated since version 4.30.0, to be removed with 5.0.0. Use the ACP3\Core\View\Renderer\Smarty\PluginTypeEnum instead
     */
    const EXTENSION_TYPE_FILTER = 'filter';
    /**
     * @deprecated since version 4.30.0, to be removed with 5.0.0. Use the ACP3\Core\View\Renderer\Smarty\PluginTypeEnum instead
     */
    const EXTENSION_TYPE_FUNCTION = 'function';
    /**
     * @deprecated since version 4.30.0, to be removed with 5.0.0. Use the ACP3\Core\View\Renderer\Smarty\PluginTypeEnum instead
     */
    const EXTENSION_TYPE_MODIFIER = 'modifier';
    /**
     * @deprecated since version 4.30.0, to be removed with 5.0.0. Use the ACP3\Core\View\Renderer\Smarty\PluginTypeEnum instead
     */
    const EXTENSION_TYPE_RESOURCE = 'resource';

    /**
     * @return string
     *
     * @deprecated since version 4.30.0, to be removed with 5.0.0
     */
    public function getExtensionType();

    /**
     * @return string
     */
    public function getExtensionName();

    /**
     * @param \Smarty $smarty
     */
    public function register(\Smarty $smarty);
}
