<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Categories\Controller\Admin\Index
 */
class Index extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @param \ACP3\Core\Controller\Context\AdminContext             $context
     * @param \ACP3\Modules\ACP3\Categories\Model\CategoryRepository $categoryRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Categories\Model\CategoryRepository $categoryRepository)
    {
        parent::__construct($context);

        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $categories = $this->categoryRepository->getAllWithModuleName();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($categories)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/categories/index/delete')
            ->setResourcePathEdit('admin/categories/index/edit');

        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('categories', 'title'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['title'],
                'default_sort' => true
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'description'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['description']
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('categories', 'module'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TranslateColumnRenderer::class,
                'fields' => ['module'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($categories) > 0
        ];
    }
}
