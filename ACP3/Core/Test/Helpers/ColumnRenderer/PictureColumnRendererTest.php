<?php
namespace ACP3\Core\Test\Helpers\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\PictureColumnRenderer;
use ACP3\Core\Router;

class PictureColumnRendererTest extends AbstractColumnRendererTest
{
    /**
     * @var Router|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $routerMock;

    protected function setUp()
    {
        $this->routerMock = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->setMethods(['route'])
            ->getMock();

        $this->columnRenderer = new PictureColumnRenderer(
            $this->routerMock
        );

        parent::setUp();
    }

    public function testValidField()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['picture'],
            'custom' => [
                'pattern' => 'gallery/index/pic/id_%s',
                'isRoute' => true
            ]
        ]);
        $this->dbData = [
            'picture' => 1
        ];

        $this->routerMock->expects($this->once())
            ->method('route')
            ->with('gallery/index/pic/id_1')
            ->willReturn('/gallery/index/pic/id_1/');

        $expected = '<td><img src="/gallery/index/pic/id_1/" alt=""></td>';
        $this->compareResults($expected);
    }

    public function testValidFieldWithNoInternalRoute()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['picture'],
            'custom' => [
                'pattern' => 'gallery/index/pic/id_%s'
            ]
        ]);
        $this->dbData = [
            'picture' => 1
        ];

        $this->routerMock->expects($this->never())
            ->method('route');

        $expected = '<td><img src="gallery/index/pic/id_1" alt=""></td>';
        $this->compareResults($expected);
    }
}
