<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

use ACP3\Core\Date;
use ACP3\Core\Date\DateTranslator;
use ACP3\Core\Helpers\Formatter\DateRange;
use ACP3\Core\I18n\Translator;

class DateColumnRendererTest extends AbstractColumnRendererTest
{
    protected $langMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
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

        $this->columnRenderer = new DateColumnRenderer(
            $this->date,
            $this->dateRange
        );

        parent::setUp();
    }

    public function testValidField()
    {
        $this->columnData = \array_merge($this->columnData, [
            'fields' => ['date'],
        ]);
        $this->dbData = [
            'date' => '2015-10-20 20:20:21',
        ];

        $expected = '<td data-sort="1445372421"><time datetime="2015-10-20T22:20:21+02:00" title="2015-10-20 22:20">2015-10-20 22:20</time></td>';
        $this->compareResults($expected);
    }

    public function testValidFieldWithDateRange()
    {
        $this->columnData = \array_merge($this->columnData, [
            'fields' => ['start', 'end'],
        ]);
        $this->dbData = [
            'start' => '2015-10-20 20:20:21',
            'end' => '2015-10-25 20:20:21',
        ];

        $expected = '<td data-sort="1445372421"><time datetime="2015-10-20T22:20:21+02:00">2015-10-20 22:20</time>&ndash;<time datetime="2015-10-25T21:20:21+01:00">2015-10-25 21:20</time></td>';
        $this->compareResults($expected);
    }
}
