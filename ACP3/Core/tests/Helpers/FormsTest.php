<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core\DataProvider\Helpers\CheckboxGeneratorDataProvider;
use ACP3\Core\DataProvider\Helpers\ChoicesGeneratorDataProvider;
use ACP3\Core\DataProvider\Helpers\RecordsPerPageDataProvider;
use ACP3\Core\DataProvider\Helpers\SelectEntryDataProvider;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;

class FormsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Forms
     */
    protected $formsHelper;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $translatorMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $requestMock;

    protected function setup(): void
    {
        $this->translatorMock = $this->createMock(Translator::class);
        $this->requestMock = $this->createMock(RequestInterface::class);

        $this->formsHelper = new Forms(
            $this->translatorMock,
            $this->requestMock
        );
    }

    /**
     * @dataProvider selectEntryDataProvider
     */
    public function testSelectEntry(string $formFieldName, mixed $defaultValue, mixed $currentValue, string $htmlAttribute, mixed $postValue, string $expected): void
    {
        $this->setUpRequestExpectations($formFieldName, $postValue);

        self::assertEquals(
            $expected,
            $this->formsHelper->selectEntry($formFieldName, $defaultValue, $currentValue, $htmlAttribute)
        );
    }

    private function setUpRequestExpectations(string $formFieldName, mixed $postValue): void
    {
        $postValues = [];
        if ($postValue !== null) {
            $postValues = [$formFieldName => $postValue];
        }

        $this->requestMock->expects($this->atLeastOnce())
            ->method('getPost')
            ->willReturn(new \Symfony\Component\HttpFoundation\ParameterBag($postValues));
    }

    /**
     * @return mixed[]
     */
    public static function selectEntryDataProvider(): array
    {
        return (new SelectEntryDataProvider())->getData();
    }

    /**
     * @dataProvider recordsPerPageDataProvider
     *
     * @param mixed[] $expected
     */
    public function testRecordsPerPage(?int $currentValue, int $steps, int $maxValue, ?int $postValue, array $expected): void
    {
        $this->setUpRequestExpectations('entries', $postValue);

        self::assertEquals($expected, $this->formsHelper->recordsPerPage($currentValue, $steps, $maxValue));
    }

    /**
     * @return mixed[]
     */
    public static function recordsPerPageDataProvider(): array
    {
        return (new RecordsPerPageDataProvider())->getData();
    }

    /**
     * @dataProvider choicesGeneratorDataProvider
     *
     * @param mixed[] $expected
     */
    public function testChoicesGenerator(mixed $currentValue, string $htmlAttribute, mixed $postValue, array $expected): void
    {
        $this->setUpRequestExpectations('form_field', $postValue);

        $values = [
            'foo' => 'Lorem',
            'bar' => 'Ipsum',
            'baz' => 'Dolor',
        ];

        self::assertEquals(
            $expected,
            $this->formsHelper->choicesGenerator('form_field', $values, $currentValue, $htmlAttribute)
        );
    }

    /**
     * @return mixed[]
     */
    public static function choicesGeneratorDataProvider(): array
    {
        return (new ChoicesGeneratorDataProvider())->getData();
    }

    /**
     * @dataProvider checkboxGeneratorDataProvider
     *
     * @param mixed[] $expected
     */
    public function testCheckboxGenerator(mixed $currentValue, mixed $postValue, array $expected): void
    {
        $this->setUpRequestExpectations('form_field', $postValue);

        $values = [
            'foo' => 'Lorem',
            'bar' => 'Ipsum',
            'baz' => 'Dolor',
        ];

        self::assertEquals(
            $expected,
            $this->formsHelper->checkboxGenerator('form_field', $values, $currentValue)
        );
    }

    /**
     * @return mixed[]
     */
    public static function checkboxGeneratorDataProvider(): array
    {
        return (new CheckboxGeneratorDataProvider())->getData();
    }
}
