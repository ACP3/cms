<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Test\DataGrid\ColumnRenderer;

use ACP3\Core\ACL;
use ACP3\Core\Test\Helpers\ColumnRenderer\AbstractColumnRendererTest;
use ACP3\Modules\ACP3\Users\DataGrid\ColumnRenderer\UserRolesColumnRenderer;

class UserRolesColumnRendererTest extends AbstractColumnRendererTest
{
    /**
     * @var ACL|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $aclMock;

    protected function setUp()
    {
        $this->aclMock = $this->createMock(ACL::class);

        $this->columnRenderer = new UserRolesColumnRenderer($this->aclMock);

        parent::setUp();
    }

    public function testValidField()
    {
        $this->aclMock->expects($this->once())
            ->method('getUserRoleNames')
            ->with(1)
            ->willReturn(['Administrator']);

        $this->columnData = \array_merge($this->columnData, [
            'fields' => ['text'],
        ]);
        $this->dbData = [
            'text' => 1,
        ];

        $expected = '<td>Administrator</td>';
        $this->compareResults($expected);
    }
}