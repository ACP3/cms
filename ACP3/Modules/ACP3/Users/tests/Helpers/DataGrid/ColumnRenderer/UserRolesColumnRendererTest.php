<?php

class UserRolesColumnRendererTest extends \AbstractColumnRendererTest
{
    /**
     * @var \ACP3\Core\ACL|PHPUnit_Framework_MockObject_MockObject
     */
    protected $aclMock;

    protected function setUp()
    {
        $this->aclMock = $this->getMockBuilder(\ACP3\Core\ACL::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserRoleNames'])
            ->getMock();

        $this->columnRenderer = new \ACP3\Modules\ACP3\Users\Helpers\DataGrid\ColumnRenderer\UserRolesColumnRenderer($this->aclMock);

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
