<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\DataGrid\ColumnRenderer;

use ACP3\Core\DataGrid\ColumnRenderer\AbstractColumnRendererTest;
use ACP3\Core\Helpers\View\Icon;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Router\RouterInterface;

class AccountStatusColumnRendererTest extends AbstractColumnRendererTest
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&Translator
     */
    private $langMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&RouterInterface
     */
    private $routerMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&Icon
     */
    private $iconMock;

    protected function setup(): void
    {
        $this->langMock = $this->createMock(Translator::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->iconMock = $this->createMock(Icon::class);

        $this->columnRenderer = new AccountStatusColumnRenderer(
            $this->langMock,
            $this->routerMock,
            $this->iconMock,
        );

        parent::setUp();
    }

    public function testValidField(): void
    {
        $this->iconMock->expects(self::atLeastOnce())
            ->method('__invoke')
            ->willReturn('icon-success');

        $this->columnData = [...$this->columnData, ...[
            'fields' => ['status'],
        ]];
        $this->dbData = [
            'id' => 123,
            'status' => 1,
        ];

        $this->primaryKey = 'id';

        $expected = '<td data-sort="1">icon-success</td>';
        $this->compareResults($expected);
    }

    public function testWithDisabledStatus(): void
    {
        $this->langMock->expects(self::once())
            ->method('t')
            ->with('newsletter', 'activate_account')
            ->willReturn('{NEWSLETTER_ACTIVATE_ACCOUNT}');

        $this->routerMock->expects(self::once())
            ->method('route')
            ->with('acp/newsletter/accounts/activate/id_123')
            ->willReturn('/index.php/acp/newsletter/accounts/activate/id_123/');

        $this->iconMock->expects(self::atLeastOnce())
            ->method('__invoke')
            ->willReturn('icon-trash');

        $this->columnData = [...$this->columnData, ...[
            'fields' => ['status'],
        ]];
        $this->dbData = [
            'id' => 123,
            'status' => 0,
        ];

        $this->primaryKey = 'id';

        $expected = '<td data-sort="0"><a href="/index.php/acp/newsletter/accounts/activate/id_123/" title="{NEWSLETTER_ACTIVATE_ACCOUNT}">icon-trash</a></td>';
        $this->compareResults($expected);
    }

    public function testDefaultValueIfNull(): void
    {
        $this->columnData = array_merge($this->columnData, [
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

    public function testDefaultValueIfNotFound(): void
    {
        $this->columnData = array_merge($this->columnData, [
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
