<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Helpers;


use ACP3\Core\Helpers\Forms;
use ACP3\Core\Http\Request;
use ACP3\Core\I18n\Translator;

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
        $this->setUpSelectEntryExpectations($formFieldName, $postValue);

        $this->assertEquals(
            $expected,
            $this->formsHelper->selectEntry($formFieldName, $defaultValue, $currentValue, $htmlAttribute)
        );
    }

    /**
     * @param string $formFieldName
     * @param mixed  $postValue
     */
    private function setUpSelectEntryExpectations($formFieldName, $postValue)
    {
        $postValues = [];
        if ($postValue !== null) {
            $postValues = [$formFieldName => $postValue];
        }

        var_dump($postValues);

        $this->requestMock->expects($this->once())
            ->method('getPost')
            ->willReturn(new Request\ParameterBag($postValues));
    }

    /**
     * @return array
     */
    public function selectEntryDataProvider()
    {
        return [
            'not_selected' => [
                'foo',
                1,
                0,
                'selected',
                null,
                ''
            ],
            'value_array_not_selected' => [
                'foo',
                '',
                [
                    'a',
                    'b',
                    'c'
                ],
                'selected',
                null,
                ''
            ],
            'value_array_selected' => [
                'foo',
                'a',
                [
                    'a',
                    'b',
                    'c'
                ],
                'selected',
                null,
                ' selected="selected"'
            ],
            'value_array_post_selected' => [
                'foo',
                'b',
                [
                    'a',
                    'c'
                ],
                'selected',
                [
                    'a',
                    'b',
                    'c'
                ],
                ' selected="selected"'
            ],
            'empty_attribute_selected' => [
                'foo',
                1,
                1,
                '',
                1,
                ' selected="selected"'
            ],
            'checked' => [
                'foo',
                1,
                1,
                'checked',
                1,
                ' checked="checked"'
            ],
        ];
    }
}
