<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\DataGrid\ColumnRenderer;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\AbstractColumnRendererTest;

class UserRolesColumnRendererTest extends AbstractColumnRendererTest
{
    protected $aclMock;

    protected function setup(): void
    {
        $this->aclMock = $this->createMock(ACL::class);

        $this->columnRenderer = new UserRolesColumnRenderer($this->aclMock);

        parent::setUp();
    }

    public function testValidField()
    {
        $this->aclMock->expects(self::once())
            ->method('getUserRoleNames')
            ->with(1)
            ->willReturn(['Administrator']);

        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text'],
        ]);
        $this->dbData = [
            'text' => 1,
        ];

        $expected = '<td>Administrator</td>';
        $this->compareResults($expected);
    }
}
