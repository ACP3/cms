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
use ACP3\Core\Http\Request;
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
        $this->requestMock = $this->createMock(Request::class);

        $this->formsHelper = new Forms(
            $this->translatorMock,
            $this->requestMock
        );
    }

    /**
     * @dataProvider selectEntryDataProvider
     *
     * @param string $formFieldName
     * @param mixed  $defaultValue
     * @param mixed  $currentValue
     * @param string $htmlAttribute
     * @param mixed  $postValue
     * @param string $expected
     */
    public function testSelectEntry($formFieldName, $defaultValue, $currentValue, $htmlAttribute, $postValue, $expected)
    {
        $this->setUpRequestExpectations($formFieldName, $postValue);

        $this->assertEquals(
            $expected,
            $this->formsHelper->selectEntry($formFieldName, $defaultValue, $currentValue, $htmlAttribute)
        );
    }

    /**
     * @param string $formFieldName
     * @param mixed  $postValue
     */
    private function setUpRequestExpectations($formFieldName, $postValue)
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
     * @return array
     */
    public function selectEntryDataProvider()
    {
        return (new SelectEntryDataProvider())->getData();
    }

    /**
     * @dataProvider recordsPerPageDataProvider
     */
    public function testRecordsPerPage(?int $currentValue, int $steps, int $maxValue, ?int $postValue, array $expected)
    {
        $this->setUpRequestExpectations('entries', $postValue);

        $this->assertEquals($expected, $this->formsHelper->recordsPerPage($currentValue, $steps, $maxValue));
    }

    /**
     * @return array
     */
    public function recordsPerPageDataProvider()
    {
        return (new RecordsPerPageDataProvider())->getData();
    }

    /**
     * @dataProvider choicesGeneratorDataProvider
     *
     * @param mixed  $currentValue
     * @param string $htmlAttribute
     * @param mixed  $postValue
     * @param array  $expected
     */
    public function testChoicesGenerator($currentValue, $htmlAttribute, $postValue, $expected)
    {
        $this->setUpRequestExpectations('form_field', $postValue);

        $values = [
            'foo' => 'Lorem',
            'bar' => 'Ipsum',
            'baz' => 'Dolor',
        ];

        $this->assertEquals(
            $expected,
            $this->formsHelper->choicesGenerator('form_field', $values, $currentValue, $htmlAttribute)
        );
    }

    /**
     * @return array
     */
    public function choicesGeneratorDataProvider()
    {
        return (new ChoicesGeneratorDataProvider())->getData();
    }

    /**
     * @dataProvider checkboxGeneratorDataProvider
     *
     * @param mixed $currentValue
     * @param mixed $postValue
     * @param array $expected
     */
    public function testCheckboxGenerator($currentValue, $postValue, $expected)
    {
        $this->setUpRequestExpectations('form_field', $postValue);

        $values = [
            'foo' => 'Lorem',
            'bar' => 'Ipsum',
            'baz' => 'Dolor',
        ];

        $this->assertEquals(
            $expected,
            $this->formsHelper->checkboxGenerator('form_field', $values, $currentValue)
        );
    }

    /**
     * @return array
     */
    public function checkboxGeneratorDataProvider()
    {
        return (new CheckboxGeneratorDataProvider())->getData();
    }
}
