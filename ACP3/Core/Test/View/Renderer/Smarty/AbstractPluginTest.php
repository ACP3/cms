<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\View\Renderer\Smarty;

use ACP3\Core\View\Renderer\Smarty\PluginInterface;

abstract class AbstractPluginTest extends \PHPUnit_Framework_TestCase
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
