<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\I18n;

use ACP3\Core\I18n\CountryList;
use ACP3\Core\I18n\Locale;

class CountryListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CountryList
     */
    private $countryList;
    /**
     * @var Locale|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeMock;

    protected function setUp()
    {
        $this->localeMock = $this->getMockBuilder(Locale::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLocale'])
            ->getMock();

        $this->countryList = new CountryList($this->localeMock);
    }

    public function testValidLocale()
    {
        $this->localeMock->expects($this->exactly(3))
            ->method('getLocale')
            ->willReturn('en_US');

        $actual = $this->countryList->worldCountries();

        $this->assertTrue(is_array($actual));
        $this->assertNotEmpty($actual);
    }

    public function testInvalidLocaleByPath()
    {
        $this->localeMock->expects($this->exactly(3))
            ->method('getLocale')
            ->willReturn('xx_ZZ');

        $actual = $this->countryList->worldCountries();

        $this->assertTrue(is_array($actual));
        $this->assertEmpty($actual);
    }

    public function testInvalidLocaleByCharacters()
    {
        $this->localeMock->expects($this->exactly(3))
            ->method('getLocale')
            ->willReturn('2390');

        $actual = $this->countryList->worldCountries();

        $this->assertTrue(is_array($actual));
        $this->assertEmpty($actual);
    }
}
