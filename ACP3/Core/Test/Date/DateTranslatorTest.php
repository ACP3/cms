<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Core\Test\Date;

use ACP3\Core\Date\DateTranslator;
use ACP3\Core\I18n\Translator;

/**
 * Class DateTranslatorTest
 * @package ACP3\Core\Test
 */
class DateTranslatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Installer\Core\I18n\Translator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $translatorMock;
    /**
     * @var DateTranslator
     */
    private $dateTranslator;

    protected function setUp()
    {
        $this->translatorMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->setMethods(['t'])
            ->getMock();

        $this->dateTranslator = new DateTranslator($this->translatorMock);
    }

    public function testAbbreviatedDaysLocalization()
    {
        $expected = [
            'Mon' => '{MON}',
            'Tue' => '{TUE}',
            'Wed' => '{WED}',
            'Thu' => '{THU}',
            'Fri' => '{FRI}',
            'Sat' => '{SAT}',
            'Sun' => '{SUN}'
        ];

        $this->setTranslatorExpectation($expected, 7);

        $this->assertEquals($expected, $this->dateTranslator->localize('D'));
    }

    public function testFullDaysLocalization()
    {
        $expected = [
            'Monday' => '{MONDAY}',
            'Tuesday' => '{TUESDAY}',
            'Wednesday' => '{WEDNESDAY}',
            'Thursday' => '{THURSDAY}',
            'Friday' => '{FRIDAY}',
            'Saturday' => '{SATURDAY}',
            'Sunday' => '{SUNDAY}'
        ];

        $this->setTranslatorExpectation($expected, 7);

        $this->assertEquals($expected, $this->dateTranslator->localize('l'));
    }

    public function testAbbreviatedMonthLocalization()
    {
        $expected = [
            'Jan' => '{JAN}',
            'Feb' => '{FEB}',
            'Mar' => '{MAR}',
            'Apr' => '{APR}',
            'May' => '{MAY}',
            'Jun' => '{JUN}',
            'Jul' => '{JUL}',
            'Aug' => '{AUG}',
            'Sep' => '{SEP}',
            'Oct' => '{OCT}',
            'Nov' => '{NOV}',
            'Dec' => '{DEC}'
        ];

        $this->setTranslatorExpectation($expected, 12);

        $this->assertEquals($expected, $this->dateTranslator->localize('M'));
    }

    public function testFullMonthsLocalization()
    {
        $expected = [
            'January' => '{JANUARY}',
            'February' => '{FEBRUARY}',
            'March' => '{MARCH}',
            'April' => '{APRIL}',
            'May' => '{MAY}',
            'June' => '{JUNE}',
            'July' => '{JULY}',
            'August' => '{AUGUST}',
            'September' => '{SEPTEMBER}',
            'October' => '{OCTOBER}',
            'November' => '{NOVEMBER}',
            'December' => '{DECEMBER}'
        ];

        $this->setTranslatorExpectation($expected, 12);

        $this->assertEquals($expected, $this->dateTranslator->localize('F'));
    }

    /**
     * @param array $data
     * @param int   $methodCallCount
     */
    private function setTranslatorExpectation(array $data, $methodCallCount)
    {
        $translations = [];
        foreach ($data as $translation) {
            $translations[] = $translation;
        }

        $invocationMocker = $this->translatorMock
            ->expects($this->exactly($methodCallCount))
            ->method('t');

        call_user_func_array([$invocationMocker, 'willReturnOnConsecutiveCalls'], $translations);
    }
}
