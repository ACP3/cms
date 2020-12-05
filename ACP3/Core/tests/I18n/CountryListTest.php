<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

class CountryListTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CountryList
     */
    private $countryList;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $translatorMock;

    protected function setup(): void
    {
        $this->translatorMock = $this->createPartialMock(Translator::class, ['getLocale']);

        $this->countryList = new CountryList($this->translatorMock);
    }

    public function testValidLocale()
    {
        $this->translatorMock->expects(self::exactly(3))
            ->method('getLocale')
            ->willReturn('en_US');

        $actual = $this->countryList->worldCountries();

        self::assertIsArray($actual);
        self::assertNotEmpty($actual);
    }

    public function testInvalidLocaleByPath()
    {
        $this->translatorMock->expects(self::exactly(3))
            ->method('getLocale')
            ->willReturn('xx_ZZ');

        $actual = $this->countryList->worldCountries();

        self::assertIsArray($actual);
        self::assertEmpty($actual);
    }

    public function testInvalidLocaleByCharacters()
    {
        $this->translatorMock->expects(self::exactly(3))
            ->method('getLocale')
            ->willReturn('2390');

        $actual = $this->countryList->worldCountries();

        self::assertIsArray($actual);
        self::assertEmpty($actual);
    }
}
