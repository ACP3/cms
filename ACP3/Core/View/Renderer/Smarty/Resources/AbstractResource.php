<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Resources;

use ACP3\Core\View\Renderer\Smarty\PluginInterface;
use ACP3\Core\View\Renderer\Smarty\PluginTypeEnum;

abstract class AbstractResource extends \Smarty_Resource_Custom implements PluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getExtensionType()
    {
        return PluginTypeEnum::RESOURCE;
    }

    /**
     * {@inheritdoc}
     */
    public function register(\Smarty $smarty)
    {
        $smarty->registerResource($this->getExtensionName(), $this);
    }
}
