<?php

namespace ACP3\Core\Test\Helpers\ColumnRenderer;

use ACP3\Core\Config;
use ACP3\Core\Date;
use ACP3\Core\Date\DateTranslator;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\DateColumnRenderer;
use ACP3\Core\Helpers\Formatter\DateRange;
use ACP3\Core\I18n\Translator;
use ACP3\Core\User;

class DateColumnRendererTest extends AbstractColumnRendererTest
{
    /**
     * @var Translator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $langMock;
    /**
     * @var User|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userMock;
    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;
    /**
     * @var DateTranslator
     */
    protected $dateTranslator;
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var DateRange
     */
    protected $dateRange;

    protected function setUp()
    {
        $this->langMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->setMethods(['t'])
            ->getMock();
        $this->dateTranslator = new DateTranslator($this->langMock);
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

        $this->date = new Date(
            $this->userMock,
            $this->dateTranslator,
            $this->configMock
        );
        $this->dateRange = new DateRange(
            $this->date,
            $this->langMock
        );

        $this->columnRenderer = new DateColumnRenderer(
            $this->date,
            $this->dateRange
        );

        parent::setUp();
    }

    public function testValidField()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['date']
        ]);
        $this->dbData = [
            'date' => '2015-10-20 20:20:21'
        ];

        $expected = '<td data-order="1445372421"><time datetime="2015-10-20T22:20:21+02:00" title="2015-10-20 22:20">2015-10-20 22:20</time></td>';
        $this->compareResults($expected);
    }

    public function testValidFieldWithDateRange()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['start', 'end']
        ]);
        $this->dbData = [
            'start' => '2015-10-20 20:20:21',
            'end' => '2015-10-25 20:20:21'
        ];

        $expected = '<td data-order="1445372421"><time datetime="2015-10-20T22:20:21+02:00">2015-10-20 22:20</time>&ndash;<time datetime="2015-10-25T21:20:21+01:00">2015-10-25 21:20</time></td>';
        $this->compareResults($expected);
    }
}
