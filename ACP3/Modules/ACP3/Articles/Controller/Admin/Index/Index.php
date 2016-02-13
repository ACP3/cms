<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin\Index
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\ArticleRepository
     */
    protected $articleRepository;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext          $context
     * @param \ACP3\Modules\ACP3\Articles\Model\ArticleRepository $articleRepository
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Articles\Model\ArticleRepository $articleRepository)
    {
        parent::__construct($context);

        $this->articleRepository = $articleRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $articles = $this->articleRepository->getAllInAcp();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($articles)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/articles/index/delete')
            ->setResourcePathEdit('admin/articles/index/edit');

        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('system', 'publication_period'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\DateColumnRenderer::class,
                'fields' => ['start', 'end']
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('articles', 'title'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['title'],
                'default_sort' => true
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($articles) > 0
        ];
    }
}
