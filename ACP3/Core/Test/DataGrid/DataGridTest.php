<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\DataGrid;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\HeaderColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\ConfigProcessor;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\Formatter\MarkEntries;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use Symfony\Component\DependencyInjection\Container;

class DataGridTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;
    /**
     * @var DataGrid
     */
    protected $dataGrid;
    /**
     * @var ConfigProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configProcessorMock;
    /**
     * @var ACL|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $aclMock;
    /**
     * @var Translator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $langMock;
    /**
     * @var Input
     */
    private $inputOptions;
    /**
     * @var Container
     */
    private $container;

    protected function setUp()
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
     * @return array
     */
    protected function getDefaultExpected()
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
            ],
        ];
    }

    public function testRenderWithDefaults()
    {
        $this->aclMock
            ->expects($this->exactly(2))
            ->method('hasPermission')
            ->willReturn(false);

        $expected = $this->getDefaultExpected();

        $this->assertEquals($expected, $this->dataGrid->render($this->inputOptions));
    }

    public function testRenderWithOneTextColumn()
    {
        $this->aclMock
            ->expects($this->exactly(2))
            ->method('hasPermission')
            ->willReturn(false);

        /** @var MarkEntries|\PHPUnit_Framework_MockObject_MockObject $markEntriesMock */
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

        $this->assertEquals($expected, $this->dataGrid->render($this->inputOptions));
    }

    public function testRenderWithOneTextColumnAndData()
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
            ->expects($this->exactly(2))
            ->method('hasPermission')
            ->willReturn(false);

        /** @var MarkEntries|\PHPUnit_Framework_MockObject_MockObject $markEntriesMock */
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

        $this->assertEquals($expected, $this->dataGrid->render($this->inputOptions));
    }
}
