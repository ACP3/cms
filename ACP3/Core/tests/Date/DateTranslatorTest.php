<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Date;

use ACP3\Core\I18n\Translator;
use PHPUnit\Framework\MockObject\MockObject;

class DateTranslatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Translator&MockObject
     */
    private $translatorMock;
    /**
     * @var DateTranslator
     */
    private $dateTranslator;

    protected function setup(): void
    {
        $this->translatorMock = $this->createMock(Translator::class);

        $this->dateTranslator = new DateTranslator($this->translatorMock);
    }

    public function testAbbreviatedDaysLocalization(): void
    {
        $expected = [
            'Mon' => '{MON}',
            'Tue' => '{TUE}',
            'Wed' => '{WED}',
            'Thu' => '{THU}',
            'Fri' => '{FRI}',
            'Sat' => '{SAT}',
            'Sun' => '{SUN}',
        ];

        $this->setTranslatorExpectation($expected, 7);

        self::assertEquals($expected, $this->dateTranslator->localize('D'));
    }

    public function testFullDaysLocalization(): void
    {
        $expected = [
            'Monday' => '{MONDAY}',
            'Tuesday' => '{TUESDAY}',
            'Wednesday' => '{WEDNESDAY}',
            'Thursday' => '{THURSDAY}',
            'Friday' => '{FRIDAY}',
            'Saturday' => '{SATURDAY}',
            'Sunday' => '{SUNDAY}',
        ];

        $this->setTranslatorExpectation($expected, 7);

        self::assertEquals($expected, $this->dateTranslator->localize('l'));
    }

    public function testAbbreviatedMonthLocalization(): void
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
            'Dec' => '{DEC}',
        ];

        $this->setTranslatorExpectation($expected, 12);

        self::assertEquals($expected, $this->dateTranslator->localize('M'));
    }

    public function testFullMonthsLocalization(): void
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
            'December' => '{DECEMBER}',
        ];

        $this->setTranslatorExpectation($expected, 12);

        self::assertEquals($expected, $this->dateTranslator->localize('F'));
    }

    /**
     * @param array<string, string> $data
     */
    private function setTranslatorExpectation(array $data, int $methodCallCount): void
    {
        $translations = [];
        foreach ($data as $translation) {
            $translations[] = $translation;
        }

        $invocationMocker = $this->translatorMock
            ->expects(self::exactly($methodCallCount))
            ->method('t');

        \call_user_func_array($invocationMocker->willReturnOnConsecutiveCalls(...), $translations);
    }
}
