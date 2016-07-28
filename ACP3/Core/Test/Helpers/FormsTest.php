<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Helpers;


use ACP3\Core\Helpers\Forms;
use ACP3\Core\Http\Request;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Test\DataProvider\Helpers\CheckboxGeneratorDataProvider;
use ACP3\Core\Test\DataProvider\Helpers\ChoicesGeneratorDataProvider;
use ACP3\Core\Test\DataProvider\Helpers\RecordsPerPageDataProvider;
use ACP3\Core\Test\DataProvider\Helpers\SelectEntryDataProvider;

class FormsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Forms
     */
    protected $formsHelper;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translatorMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    protected function setUp()
    {
        $this->translatorMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->setMethods(['t'])
            ->getMock();
        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

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
     *
     * @param integer $currentValue
     * @param integer $steps
     * @param integer $maxValue
     * @param integer $postValue
     * @param array   $expected
     */
    public function testRecordsPerPage($currentValue, $steps, $maxValue, $postValue, $expected)
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
            'baz' => 'Dolor'
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
     * @param mixed  $currentValue
     * @param mixed  $postValue
     * @param array  $expected
     */
    public function testCheckboxGenerator($currentValue, $postValue, $expected)
    {
        $this->setUpRequestExpectations('form_field', $postValue);

        $values = [
            'foo' => 'Lorem',
            'bar' => 'Ipsum',
            'baz' => 'Dolor'
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
