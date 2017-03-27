<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\I18n;

use ACP3\Core\I18n\CountryList;
use ACP3\Core\I18n\Translator;

class CountryListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CountryList
     */
    private $countryList;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $translatorMock;

    protected function setUp()
    {
        $this->translatorMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLocale'])
            ->getMock();

        $this->countryList = new CountryList($this->translatorMock);
    }

    public function testValidLocale()
    {
        $this->translatorMock->expects($this->exactly(6))
            ->method('getLocale')
            ->willReturn('en_US');

        $actual = $this->countryList->worldCountries();

        $this->assertTrue(is_array($actual));
        $this->assertNotEmpty($actual);
    }

    public function testInvalidLocaleByPath()
    {
        $this->translatorMock->expects($this->exactly(3))
            ->method('getLocale')
            ->willReturn('xx_ZZ');

        $actual = $this->countryList->worldCountries();

        $this->assertTrue(is_array($actual));
        $this->assertEmpty($actual);
    }

    public function testInvalidLocaleByCharacters()
    {
        $this->translatorMock->expects($this->exactly(3))
            ->method('getLocale')
            ->willReturn('2390');

        $actual = $this->countryList->worldCountries();

        $this->assertTrue(is_array($actual));
        $this->assertEmpty($actual);
    }
}
