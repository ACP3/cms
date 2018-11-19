<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Test\DataGrid\ColumnRenderer;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Test\DataGrid\ColumnRenderer\AbstractColumnRendererTest;
use ACP3\Modules\ACP3\Newsletter\DataGrid\ColumnRenderer\AccountStatusColumnRenderer;

class AccountStatusColumnRendererTest extends AbstractColumnRendererTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $langMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $routerMock;

    protected function setUp()
    {
        $this->langMock = $this->createMock(Translator::class);
        $this->routerMock = $this->createMock(RouterInterface::class);

        $this->columnRenderer = new AccountStatusColumnRenderer(
            $this->langMock,
            $this->routerMock
        );

        parent::setUp();
    }

    public function testValidField()
    {
        $this->columnData = \array_merge($this->columnData, [
            'fields' => ['status'],
        ]);
        $this->dbData = [
            'id' => 123,
            'status' => 1,
        ];

        $this->primaryKey = 'id';

        $expected = '<td data-sort="1"><i class="glyphicon glyphicon-ok text-success"></i></td>';
        $this->compareResults($expected);
    }

    public function testWithDisabledStatus()
    {
        $this->langMock->expects($this->once())
            ->method('t')
            ->with('newsletter', 'activate_account')
            ->willReturn('{NEWSLETTER_ACTIVATE_ACCOUNT}');

        $this->routerMock->expects($this->once())
            ->method('route')
            ->with('acp/newsletter/accounts/activate/id_123')
            ->willReturn('/index.php/acp/newsletter/accounts/activate/id_123/');

        $this->columnData = \array_merge($this->columnData, [
            'fields' => ['status'],
        ]);
        $this->dbData = [
            'id' => 123,
            'status' => 0,
        ];

        $this->primaryKey = 'id';

        $expected = '<td data-sort="0"><a href="/index.php/acp/newsletter/accounts/activate/id_123/" title="{NEWSLETTER_ACTIVATE_ACCOUNT}"><i class="glyphicon glyphicon-remove text-danger"></i></a></td>';
        $this->compareResults($expected);
    }

    public function testDefaultValueIfNull()
    {
        $this->columnData = \array_merge($this->columnData, [
            'fields' => ['text'],
            'custom' => [
                'default_value' => 'Foo Bar',
            ],
        ]);
        $this->dbData = [
            'text' => null,
        ];

        $expected = '<td data-sort="Foo Bar">Foo Bar</td>';
        $this->compareResults($expected);
    }

    public function testDefaultValueIfNotFound()
    {
        $this->columnData = \array_merge($this->columnData, [
            'fields' => ['test'],
            'custom' => [
                'default_value' => 'Foo Bar',
            ],
        ]);
        $this->dbData = [
            'text' => 'Lorem Ipsum',
        ];

        $expected = '<td data-sort="Foo Bar">Foo Bar</td>';
        $this->compareResults($expected);
    }
}
