<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty;

use PHPUnit\Framework\TestCase;

abstract class AbstractPluginTestCase extends TestCase
{
    /**
     * @var PluginInterface
     */
    protected $plugin;
}
