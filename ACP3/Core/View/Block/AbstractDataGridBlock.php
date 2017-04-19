<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block;


use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\Model\Repository\DataGridRepository;

abstract class AbstractDataGridBlock extends AbstractBlock implements DataGridBlockInterface
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    /**
     * @var DataGrid|null
     */
    private $dataGrid;
    /**
     * @var DataGridRepository
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
    public function setDataGridRepository(DataGridRepository $dataGridRepository)
    {
        $this->dataGridRepository = $dataGridRepository;

        return $this;
    }

    /**
     * @return DataGrid
     */
    protected function getNewDataGridInstance(): DataGrid
    {
        return $this->container->get('core.helpers.data_grid');
    }

    /**
     * @return DataGrid
     */
    protected function getCurrentDataGrid(): DataGrid
    {
        if (!$this->dataGrid) {
            $this->dataGrid = $this->getNewDataGridInstance();
        }

        return $this->dataGrid;
    }

    /**
     * @param DataGrid $dataGrid
     * @param array $dataGridOptions
     */
    protected function configureDataGrid(DataGrid $dataGrid, array $dataGridOptions)
    {
        $dataGrid
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage($this->getModuleName()))
            ->setIdentifier($dataGridOptions['identifier'])
            ->setResourcePathDelete($dataGridOptions['resource_path_delete'])
            ->setResourcePathEdit($dataGridOptions['resource_path_edit']);

        if ($this->getDataGridRepository() instanceof DataGridRepository) {
            $dataGrid->setRepository($this->getDataGridRepository());
        } else {
            $data = $this->getData();
            $dataGrid->setResults(isset($data['results']) ? $data['results'] : $data);
        }

        $this->addDataGridColumns($dataGrid);
    }

    /**
     * @param DataGrid $dataGrid
     * @return void
     */
    abstract protected function addDataGridColumns(DataGrid $dataGrid);
}
