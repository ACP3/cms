<?php

namespace ACP3\Core\Test\Helpers\Formatter;

use ACP3\Core\Config;
use ACP3\Core\Date;
use ACP3\Core\Date\DateTranslator;
use ACP3\Core\Helpers\Formatter\DateRange;
use ACP3\Core\I18n\Translator;
use ACP3\Core\User;

class DateRangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;
    /**
     * @var DateTranslator
     */
    private $dateTranslator;
    /**
     * @var Date
     */
    private $date;
    /**
     * @var Translator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $langMock;
    /**
     * @var User|\PHPUnit_Framework_MockObject_MockObject
     */
    private $userMock;

    /**
     * @var DateRange
     */
    private $dateRange;

    protected function setUp()
    {
        $this->langMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->setMethods(['t'])
            ->getMock();
        $this->userMock = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userMock->expects($this->once())
            ->method('getUserInfo')
            ->willReturn([
                'date_format_long' => 'Y-m-d H:i',
                'date_format_short' => 'Y-m-d',
                'time_zone' => 'Europe/Berlin',
            ]);

        $this->dateTranslator = new DateTranslator($this->langMock);

        $this->date = new Date(
            $this->userMock,
            $this->dateTranslator,
            $this->configMock
        );
        $this->dateRange = new DateRange(
            $this->date,
            $this->langMock
        );
    }

    /**
     * @param string $langKey
     * @param string $langValue
     * @param array  $params
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
        $this->setUpLangMockExpectation('date_published_since', 'Published since 2012-12-20 13:12',
            ['%date%' => '2012-12-20 13:12']);
        $dateStart = '2012-12-20 12:12:12';
        $dateEnd = '2012-12-19 12:12:12';
        $expected = '<time datetime="2012-12-20T13:12:12+01:00" title="Published since 2012-12-20 13:12">2012-12-20 13:12</time>';

        $this->assertEquals($expected, $this->dateRange->formatTimeRange($dateStart, $dateEnd));
    }

    public function testInvalidDateRangeWithShortFormat()
    {
        $this->setUpLangMockExpectation('date_published_since', 'Published since 2012-12-20',
            ['%date%' => '2012-12-20']);
        $dateStart = '2012-12-20 12:12:12';
        $dateEnd = '2012-12-19 12:12:12';
        $expected = '<time datetime="2012-12-20T13:12:12+01:00" title="Published since 2012-12-20">2012-12-20</time>';

        $this->assertEquals($expected, $this->dateRange->formatTimeRange($dateStart, $dateEnd, 'short'));
    }

}
