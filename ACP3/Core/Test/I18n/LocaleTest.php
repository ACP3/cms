<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\I18n;

use ACP3\Core\I18n\DictionaryInterface;
use ACP3\Core\I18n\Locale;
use ACP3\Core\Settings\SettingsInterface;

class LocaleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Locale
     */
    private $locale;
    /**
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settingsMock;
    /**
     * @var DictionaryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dictionaryMock;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->locale = new Locale($this->settingsMock, $this->dictionaryMock);
    }

    private function setUpMockObjects()
    {
        $this->settingsMock = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $this->dictionaryMock = $this->getMockBuilder(DictionaryInterface::class)
            ->setMethods(['getDictionary', 'saveDictionary'])
            ->getMock();
    }

    public function testShortIsoCode()
    {
        $this->settingsMock->expects($this->exactly(2))
            ->method('getSettings')
            ->with('system')
            ->willReturn(['lang' => 'en_US']);

        $this->assertEquals('en', $this->locale->getShortIsoCode());
    }
}
