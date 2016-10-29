<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\View\Renderer\Smarty\Modifiers;


use ACP3\Core\View\Renderer\Smarty\Modifiers\PrefixUri;

class PrefixUriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PrefixUri
     */
    private $prefixUri;

    protected function setUp()
    {
        $this->prefixUri = new PrefixUri();
    }

    public function testAddUriPrefix()
    {
        $value = 'www.example.com';
        $expected = 'http://www.example.com';
        $this->assertEquals($expected, $this->prefixUri->process($value));
    }

    public function testAddUriPrefixNotNeeded()
    {
        $value = 'http://www.example.com';
        $expected = 'http://www.example.com';
        $this->assertEquals($expected, $this->prefixUri->process($value));
    }
}
