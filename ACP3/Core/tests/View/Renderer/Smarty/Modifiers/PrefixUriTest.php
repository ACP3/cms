<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

use ACP3\Core\View\Renderer\Smarty\AbstractPluginTest;

class PrefixUriTest extends AbstractPluginTest
{
    /**
     * @var PrefixUri
     */
    protected $plugin;

    protected function setup(): void
    {
        $this->plugin = new PrefixUri();
    }

    public function testAddUriPrefix()
    {
        $value = 'www.example.com';
        $expected = 'http://www.example.com';
        self::assertEquals($expected, $this->plugin->__invoke($value));
    }

    public function testAddUriPrefixNotNeeded()
    {
        $value = 'http://www.example.com';
        $expected = 'http://www.example.com';
        self::assertEquals($expected, $this->plugin->__invoke($value));
    }
}
