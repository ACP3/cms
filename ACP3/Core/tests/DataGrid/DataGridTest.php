<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\HeaderColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\Helpers\Formatter\MarkEntries;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

class DataGridTest extends TestCase
{
    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;
    /**
     * @var DataGrid
     */
    private $dataGrid;
    /**
     * @var ConfigProcessor|MockObject
     */
    private $configProcessorMock;
    /**
     * @var ACL|MockObject
     */
    private $aclMock;
    /**
     * @var Translator|MockObject
     */
    private $langMock;
    /**
     * @var Input
     */
    private $inputOptions;
    /**
     * @var Container
     */
    private $container;

    protected function setup(): void
    {
        $this->container = new Container();
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->configProcessorMock = $this->createMock(ConfigProcessor::class);
        $this->aclMock = $this->createMock(ACL::class);
        $this->langMock = $this->createMock(Translator::class);

        $this->dataGrid = new DataGrid(
            $this->container,
            $this->requestMock,
            $this->configProcessorMock,
            $this->aclMock,
            $this->langMock
        );
        $this->inputOptions = (new Input())
            ->setIdentifier('#data-grid');

        parent::setUp();
    }

    /**
     * @return mixed[]
     */
    protected function getDefaultExpected(): array
    {
        return [
            'grid' => [
                'can_edit' => false,
                'can_delete' => false,
                'identifier' => 'data-grid',
                'header' => '',
                'config' => [],
                'results' => '',
                'num_results' => 0,
                'show_mass_delete' => false,
                'column_count' => 1,
            ],
        ];
    }

    public function testRenderWithDefaults(): void
    {
        $this->aclMock
            ->expects(self::exactly(2))
            ->method('hasPermission')
            ->willReturn(false);

        $expected = $this->getDefaultExpected();

        self::assertEquals($expected, $this->dataGrid->render($this->inputOptions));
    }

    public function testRenderWithOneTextColumn(): void
    {
        $this->aclMock
            ->expects(self::exactly(2))
            ->method('hasPermission')
            ->willReturn(false);

        /** @var MarkEntries|MockObject $markEntriesMock */
        $markEntriesMock = $this->createMock(MarkEntries::class);

        $this->container->set(HeaderColumnRenderer::class, new HeaderColumnRenderer($markEntriesMock));
        $this->container->set(TextColumnRenderer::class, new TextColumnRenderer());

        $this->inputOptions->addColumn([
            'label' => 'Foo',
            'fields' => ['title'],
            'type' => TextColumnRenderer::class,
        ], 10);

        $expected = $this->getDefaultExpected();
        $expected['grid']['header'] = '<th>Foo</th>';
        $expected['grid']['column_count'] = 2;

        self::assertEquals($expected, $this->dataGrid->render($this->inputOptions));
    }

    public function testRenderWithOneTextColumnAndData(): void
    {
        $data = [
            [
                'title' => 'Lorem Ipsum',
            ],
            [
                'title' => 'Lorem Ipsum Dolor',
            ],
        ];
        $this->aclMock
            ->expects(self::exactly(2))
            ->method('hasPermission')
            ->willReturn(false);

        /** @var MarkEntries|MockObject $markEntriesMock */
        $markEntriesMock = $this->createMock(MarkEntries::class);

        $this->container->set(HeaderColumnRenderer::class, new HeaderColumnRenderer($markEntriesMock));
        $this->container->set(TextColumnRenderer::class, new TextColumnRenderer());

        $this->inputOptions->addColumn([
            'label' => 'Foo',
            'fields' => ['title'],
            'type' => TextColumnRenderer::class,
        ], 10);
        $this->inputOptions->setResults($data);

        $expected = $this->getDefaultExpected();
        $expected['grid']['header'] = '<th>Foo</th>';
        $expected['grid']['results'] = "<tr><td>Lorem Ipsum</td></tr>\n<tr><td>Lorem Ipsum Dolor</td></tr>\n";
        $expected['grid']['num_results'] = 2;
        $expected['grid']['column_count'] = 2;

        self::assertEquals($expected, $this->dataGrid->render($this->inputOptions));
    }
}
