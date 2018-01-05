<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Resources;

use ACP3\Core\View\Renderer\Smarty\PluginInterface;

abstract class AbstractResource extends \Smarty_Resource_Custom implements PluginInterface
{
    public function getExtensionType()
    {
        return PluginInterface::EXTENSION_TYPE_RESOURCE;
    }

    /**
     * @inheritdoc
     */
    public function register(\Smarty $smarty)
    {
        $smarty->registerResource($this->getExtensionName(), $this);
    }
}
