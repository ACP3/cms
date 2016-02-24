<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Index
 */
class Index extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository
     */
    protected $galleryRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext         $context
     * @param \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository $galleryRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Gallery\Model\GalleryRepository $galleryRepository
    )
    {
        parent::__construct($context);

        $this->galleryRepository = $galleryRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $galleries = $this->galleryRepository->getAllInAcp();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($galleries)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/gallery/index/delete')
            ->setResourcePathEdit('admin/gallery/index/edit');

        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('system', 'publication_period'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\DateColumnRenderer::class,
                'fields' => ['start', 'end'],
                'default_sort' => true
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('gallery', 'title'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['title'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('gallery', 'pictures'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['pictures'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($galleries) > 0
        ];
    }
}
