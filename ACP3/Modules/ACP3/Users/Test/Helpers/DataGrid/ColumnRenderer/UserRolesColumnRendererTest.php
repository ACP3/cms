<?php

namespace ACP3\Modules\ACP3\Users\Test\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\ACL;
use ACP3\Core\Test\Helpers\ColumnRenderer\AbstractColumnRendererTest;
use ACP3\Modules\ACP3\Users\Helpers\DataGrid\ColumnRenderer\UserRolesColumnRenderer;

class UserRolesColumnRendererTest extends AbstractColumnRendererTest
{
    /**
     * @var ACL|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $aclMock;

    protected function setUp()
    {
        $this->aclMock = $this->getMockBuilder(ACL::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserRoleNames'])
            ->getMock();

        $this->columnRenderer = new UserRolesColumnRenderer($this->aclMock);

        parent::setUp();
    }

    public function testValidField()
    {
        $this->aclMock->expects($this->once())
            ->method('getUserRoleNames')
            ->with(1)
            ->willReturn(['Administrator']);

        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text']
        ]);
        $this->dbData = [
            'text' => 1
        ];

        $expected = '<td>Administrator</td>';
        $this->compareResults($expected);
    }
}
