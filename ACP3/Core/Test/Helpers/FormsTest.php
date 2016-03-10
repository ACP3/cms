<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Helpers;


use ACP3\Core\Helpers\Forms;
use ACP3\Core\Http\Request;
use ACP3\Core\I18n\Translator;
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
            ->willReturn(new Request\ParameterBag($postValues));
    }

    /**
     * @return array
     */
    public function selectEntryDataProvider()
    {
        return (new SelectEntryDataProvider())->getData();
    }

    /**
     * @return array
     */
    public function recordsPerPageDataProvider()
    {
        return (new RecordsPerPageDataProvider())->getData();
    }
}
