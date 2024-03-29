<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\ViewProviders;

use ACP3\Core\ACL;
use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\DataGrid\ColumnRenderer\PictureColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\RouteColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\SortColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator;
use ACP3\Modules\ACP3\Gallery\Helpers;
use ACP3\Modules\ACP3\Gallery\Repository\GalleryPicturesDataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class PictureDataGridViewProvider
{
    public function __construct(private readonly ACL $acl, private readonly DataGrid $dataGrid, private readonly GalleryPicturesDataGridRepository $dataGridRepository, private readonly ResultsPerPage $resultsPerPage, private readonly Steps $breadcrumb, private readonly ThumbnailGenerator $thumbnailGenerator, private readonly Title $title, private readonly Translator $translator)
    {
    }

    /**
     * @param array<string, mixed> $gallery
     *
     * @return array<string, array<string, mixed>>|JsonResponse
     */
    public function __invoke(int $id, array $gallery): array|JsonResponse
    {
        $dataGrid = $this->dataGrid->render($this->configureDataGrid($id));
        if ($dataGrid instanceof JsonResponse) {
            return $dataGrid;
        }

        $this->breadcrumb->append($gallery['title'], 'acp/gallery/pictures/index/id_' . $id);
        $this->title->setPageTitlePrefix($this->translator->t('gallery', 'admin_pictures_index'));

        return [...$dataGrid, ...['gallery_id' => $id]];
    }

    private function configureDataGrid(int $id): Input
    {
        $input = (new Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#gallery-pictures-data-grid')
            ->setResourcePathDelete('admin/gallery/pictures/delete/id_' . $id)
            ->setResourcePathEdit('admin/gallery/pictures/edit')
            ->setQueryOptions(new QueryOption('gallery_id', (string) $id))
            ->addColumn([
                'label' => $this->translator->t('gallery', 'picture'),
                'type' => PictureColumnRenderer::class,
                'fields' => ['file'],
                'custom' => [
                    'callback' => fn (string $fileName) => $this->thumbnailGenerator->generateThumbnail($fileName, 'thumb')->getFileWeb(),
                ],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('gallery', 'title'),
                'type' => TextColumnRenderer::class,
                'fields' => ['title'],
                'class' => 'w-100',
            ], 35)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => RouteColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'custom' => [
                    'path' => Helpers::URL_KEY_PATTERN_PICTURE,
                ],
            ], 10);

        if ($this->acl->hasPermission('admin/gallery/pictures/order')) {
            $input
                ->addColumn([
                    'label' => $this->translator->t('system', 'order'),
                    'type' => SortColumnRenderer::class,
                    'fields' => ['pic'],
                    'class' => 'text-center text-nowrap',
                    'default_sort' => true,
                    'custom' => [
                        'route_sort_down' => 'acp/gallery/pictures/order/id_%d/action_down',
                        'route_sort_up' => 'acp/gallery/pictures/order/id_%d/action_up',
                    ],
                ], 20);
        }

        return $input;
    }
}
