<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Block;

use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;

abstract class AbstractDataGridBlock extends AbstractBlock implements DataGridBlockInterface
{
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;
    /**
     * @var DataGrid|null
     */
    private $dataGrid;
    /**
     * @var AbstractDataGridRepository
     */
    private $dataGridRepository;

    /**
     * AbstractDataGridBlock constructor.
     * @param Context\DataGridBlockContext $context
     */
    public function __construct(Context\DataGridBlockContext $context)
    {
        parent::__construct($context);

        $this->resultsPerPage = $context->getResultsPerPage();
        $this->container = $context->getContainer();
        $this->translator = $context->getTranslator();
    }

    /**
     * @inheritdoc
     */
    public function getDataGridRepository()
    {
        return $this->dataGridRepository;
    }

    /**
     * @inheritdoc
     */
    public function setDataGridRepository(AbstractDataGridRepository $dataGridRepository)
    {
        $this->dataGridRepository = $dataGridRepository;

        return $this;
    }

    /**
     * @return DataGrid
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getCurrentDataGrid(): DataGrid
    {
        if (!$this->dataGrid) {
            $this->dataGrid = $this->getNewDataGridInstance();
        }

        return $this->dataGrid;
    }

    /**
     * @return DataGrid
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getNewDataGridInstance(): DataGrid
    {
        return $this->container->get('core.helpers.data_grid');
    }

    /**
     * @param DataGrid $dataGrid
     * @param array $dataGridOptions
     */
    protected function configureDataGrid(DataGrid $dataGrid, array $dataGridOptions)
    {
        $dataGridOptions = \array_merge($this->getDefaultDataGridOptions(), $dataGridOptions);

        $options = (new DataGrid\Options())
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage($this->getModuleName()))
            ->setUseAjax($dataGridOptions['ajax'])
            ->setIdentifier($dataGridOptions['identifier'])
            ->setResourcePathDelete($dataGridOptions['resource_path_delete'])
            ->setResourcePathEdit($dataGridOptions['resource_path_edit']);

        $dataGrid->setOptions($options);

        if ($this->getDataGridRepository() instanceof AbstractDataGridRepository) {
            $dataGrid
                ->setRepository($this->getDataGridRepository())
                ->setQueryOptions(...$dataGridOptions['query_options']);
        } else {
            $data = $this->getData();
            $dataGrid->setResults($data['results'] ?? $data);
        }

        $this->addDataGridColumns($dataGrid);
    }

    /**
     * @return array
     */
    private function getDefaultDataGridOptions(): array
    {
        return [
            'resource_path_delete' => '',
            'resource_path_edit' => '',
            'ajax' => false,
            'query_options' => [],
        ];
    }

    /**
     * @param DataGrid $dataGrid
     * @return void
     */
    abstract protected function addDataGridColumns(DataGrid $dataGrid);
}
