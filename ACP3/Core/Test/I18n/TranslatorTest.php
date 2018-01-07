<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\I18n;

use ACP3\Core\I18n\DictionaryInterface;
use ACP3\Core\I18n\LocaleInterface;
use ACP3\Core\I18n\Translator;

class TranslatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DictionaryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dictionaryMock;
    /**
     * @var LocaleInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeMock;
    /**
     * @var Translator
     */
    private $translator;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->translator = new Translator($this->dictionaryMock, $this->localeMock);
    }

    private function setUpMockObjects()
    {
        $this->dictionaryMock = $this->getMockBuilder(DictionaryInterface::class)
            ->setMethods(['getDictionary', 'saveDictionary'])
            ->getMock();

        $this->localeMock = $this->getMockBuilder(LocaleInterface::class)
            ->setMethods(['getLocale', 'getShortIsoCode', 'getDirection'])
            ->getMock();
    }

    public function testExistingTranslationPhrase()
    {
        $this->localeMock->expects($this->exactly(5))
            ->method('getLocale')
            ->willReturn('en_US');

        $this->dictionaryMock->expects($this->once())
            ->method('getDictionary')
            ->with('en_US')
            ->willReturn([
                'keys' => [
                    'foofoobar' => 'foobar',
                ],
            ]);

        $this->assertEquals('foobar', $this->translator->t('foo', 'foobar'));
    }

    public function testNotExistingTranslationPhrase()
    {
        $this->localeMock->expects($this->exactly(4))
            ->method('getLocale')
            ->willReturn('en_US');

        $this->dictionaryMock->expects($this->once())
            ->method('getDictionary')
            ->with('en_US')
            ->willReturn([
                'keys' => [
                    'foofoobar2' => 'foobar',
                ],
            ]);

        $this->assertEquals('{FOO_FOOBAR}', $this->translator->t('foo', 'foobar'));
    }
}
