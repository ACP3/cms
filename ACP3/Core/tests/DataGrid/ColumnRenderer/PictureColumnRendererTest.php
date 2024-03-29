<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

use ACP3\Core\Router\RouterInterface;

class PictureColumnRendererTest extends AbstractColumnRendererTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $routerMock;

    protected function setup(): void
    {
        $this->routerMock = $this->createMock(RouterInterface::class);

        $this->columnRenderer = new PictureColumnRenderer(
            $this->routerMock
        );

        parent::setUp();
    }

    public function testValidField(): void
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['picture'],
            'custom' => [
                'pattern' => 'gallery/index/pic/id_%s',
                'isRoute' => true,
            ],
        ]);
        $this->dbData = [
            'picture' => 1,
        ];

        $this->routerMock->expects(self::once())
            ->method('route')
            ->with('gallery/index/pic/id_1')
            ->willReturn('/gallery/index/pic/id_1/');

        $expected = '<td><img src="/gallery/index/pic/id_1/" loading="lazy" alt=""></td>';
        $this->compareResults($expected);
    }

    public function testValidFieldWithNoInternalRoute(): void
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['picture'],
            'custom' => [
                'pattern' => 'gallery/index/pic/id_%s',
            ],
        ]);
        $this->dbData = [
            'picture' => 1,
        ];

        $this->routerMock->expects($this->never())
            ->method('route');

        $expected = '<td><img src="gallery/index/pic/id_1" loading="lazy" alt=""></td>';
        $this->compareResults($expected);
    }
}
