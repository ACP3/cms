<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty;

abstract class AbstractPluginTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PluginInterface
     */
    protected $plugin;

    public function testExtensionName()
    {
        $this->assertEquals($this->getExpectedExtensionName(), $this->plugin->getExtensionName());
    }

    /**
     * @return string
     */
    abstract protected function getExpectedExtensionName();
}
