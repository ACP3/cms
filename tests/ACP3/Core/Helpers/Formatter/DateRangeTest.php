<?php

class DateRangeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\Config|PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Core\Lang|\PHPUnit_Framework_MockObject_MockObject
     */
    private $langMock;
    /**
     * @var \ACP3\Core\User|PHPUnit_Framework_MockObject_MockObject
     */
    private $userMock;

    /**
     * @var \ACP3\Core\Helpers\Formatter\DateRange
     */
    private $dateRange;

    protected function setUp()
    {
        $this->langMock = $this->getMockBuilder(\ACP3\Core\Lang::class)
            ->disableOriginalConstructor()
            ->setMethods(['t'])
            ->getMock();
        $this->userMock = $this->getMockBuilder(\ACP3\Core\User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configMock = $this->getMockBuilder(\ACP3\Core\Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userMock->expects($this->once())
            ->method('getUserInfo')
            ->willReturn([
                'date_format_long' => 'Y-m-d H:i',
                'date_format_short' => 'Y-m-d',
                'time_zone' => 'Europe/Berlin',
            ]);

        $this->date = new \ACP3\Core\Date(
            $this->userMock,
            $this->langMock,
            $this->configMock
        );
        $this->dateRange = new \ACP3\Core\Helpers\Formatter\DateRange(
            $this->date,
            $this->langMock
        );
    }

    /**
     * @param string $langKey
     * @param string $langValue
     */
    private function setUpLangMockExpectation($langKey, $langValue)
    {
        $this->langMock->expects($this->once())
            ->method('t')
            ->with('system', $langKey)
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
        $this->setUpLangMockExpectation('date_published_since', 'Published since %s');
        $dateStart = '2012-12-20 12:12:12';
        $dateEnd = '2012-12-19 12:12:12';
        $expected = '<time datetime="2012-12-20T13:12:12+01:00" title="Published since 2012-12-20 13:12">2012-12-20 13:12</time>';

        $this->assertEquals($expected, $this->dateRange->formatTimeRange($dateStart, $dateEnd));
    }

    public function testInvalidDateRangeWithShortFormat()
    {
        $this->setUpLangMockExpectation('date_published_since', 'Published since %s');
        $dateStart = '2012-12-20 12:12:12';
        $dateEnd = '2012-12-19 12:12:12';
        $expected = '<time datetime="2012-12-20T13:12:12+01:00" title="Published since 2012-12-20">2012-12-20</time>';

        $this->assertEquals($expected, $this->dateRange->formatTimeRange($dateStart, $dateEnd, 'short'));
    }

}