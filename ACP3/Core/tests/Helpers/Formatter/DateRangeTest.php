<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\Formatter;

use ACP3\Core\Date;
use ACP3\Core\Date\DateTranslator;
use ACP3\Core\I18n\Translator;

class DateRangeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DateTranslator
     */
    private $dateTranslator;
    /**
     * @var Date
     */
    private $date;

    private $langMock;
    /**
     * @var DateRange
     */
    private $dateRange;

    protected function setup(): void
    {
        $this->langMock = $this->createMock(Translator::class);

        $this->dateTranslator = new DateTranslator($this->langMock);

        $this->date = new Date(
            $this->dateTranslator
        );
        $this->date
            ->setDateFormatLong('Y-m-d H:i')
            ->setDateFormatShort('Y-m-d')
            ->setDateTimeZone(new \DateTimeZone('Europe/Berlin'));

        $this->dateRange = new DateRange(
            $this->date,
            $this->langMock
        );
    }

    /**
     * @param string $langKey
     * @param string $langValue
     */
    private function setUpLangMockExpectation($langKey, $langValue, array $params = [])
    {
        $this->langMock->expects($this->once())
            ->method('t')
            ->with('system', $langKey, $params)
            ->willReturn($langValue);
    }

    public function testSingleDateWithLongFormat()
    {
        $dateString = '2012-12-20 12:12:12';
        $expected = '<time datetime="2012-12-20T13:12:12+01:00" title="2012-12-20 13:12">2012-12-20 13:12</time>';

        $this->assertEquals($expected, $this->dateRange->formatTimeRange($dateString));
    }

    public function testSingleDateWithShortFormat()
    {
        $dateString = '2012-12-20 12:12:12';
        $expected = '<time datetime="2012-12-20T13:12:12+01:00" title="2012-12-20">2012-12-20</time>';

        $this->assertEquals($expected, $this->dateRange->formatTimeRange($dateString, '', 'short'));
    }

    public function testDateRangeWithLongFormat()
    {
        $dateStart = '2012-12-20 12:12:12';
        $dateEnd = '2012-12-25 12:12:12';
        $expected = '<time datetime="2012-12-20T13:12:12+01:00">2012-12-20 13:12</time>&ndash;<time datetime="2012-12-25T13:12:12+01:00">2012-12-25 13:12</time>';

        $this->assertEquals($expected, $this->dateRange->formatTimeRange($dateStart, $dateEnd));
    }

    public function testInvalidDateRangeWithLongFormat()
    {
        $this->setUpLangMockExpectation(
            'date_published_since',
            'Published since 2012-12-20 13:12',
            ['%date%' => '2012-12-20 13:12']
        );
        $dateStart = '2012-12-20 12:12:12';
        $dateEnd = '2012-12-19 12:12:12';
        $expected = '<time datetime="2012-12-20T13:12:12+01:00" title="Published since 2012-12-20 13:12">2012-12-20 13:12</time>';

        $this->assertEquals($expected, $this->dateRange->formatTimeRange($dateStart, $dateEnd));
    }

    public function testInvalidDateRangeWithShortFormat()
    {
        $this->setUpLangMockExpectation(
            'date_published_since',
            'Published since 2012-12-20',
            ['%date%' => '2012-12-20']
        );
        $dateStart = '2012-12-20 12:12:12';
        $dateEnd = '2012-12-19 12:12:12';
        $expected = '<time datetime="2012-12-20T13:12:12+01:00" title="Published since 2012-12-20">2012-12-20</time>';

        $this->assertEquals($expected, $this->dateRange->formatTimeRange($dateStart, $dateEnd, 'short'));
    }
}
