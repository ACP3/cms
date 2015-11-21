<?php

class DateColumnRendererTest extends AbstractColumnRendererTest
{
    /**
     * @var \ACP3\Core\Lang|PHPUnit_Framework_MockObject_MockObject
     */
    protected $langMock;
    /**
     * @var \ACP3\Core\User|PHPUnit_Framework_MockObject_MockObject
     */
    protected $userMock;
    /**
     * @var \ACP3\Core\Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Formatter\DateRange
     */
    protected $dateRange;

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

        $this->columnRenderer = new \ACP3\Core\Helpers\DataGrid\ColumnRenderer\DateColumnRenderer(
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
}