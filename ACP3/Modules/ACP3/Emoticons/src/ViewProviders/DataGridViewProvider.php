<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\ViewProviders;

use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\PictureColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Emoticons\Repository\DataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataGridViewProvider
{
    public function __construct(private readonly ApplicationPath $applicationPath, private readonly DataGrid $dataGrid, private readonly DataGridRepository $dataGridRepository, private readonly ResultsPerPage $resultsPerPage, private readonly Translator $translator)
    {
    }

    /**
     * @return array<string, array<string, mixed>>|JsonResponse
     */
    public function __invoke(): array|JsonResponse
    {
        return $this->dataGrid->render($this->configureDataGrid());
    }

    private function configureDataGrid(): Input
    {
        return (new Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#emoticons-data-grid')
            ->setResourcePathDelete('admin/emoticons/index/delete')
            ->setResourcePathEdit('admin/emoticons/index/edit')
            ->addColumn([
                'label' => $this->translator->t('system', 'description'),
                'type' => TextColumnRenderer::class,
                'fields' => ['description'],
                'class' => 'w-100',
                'default_sort' => true,
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('emoticons', 'code'),
                'type' => TextColumnRenderer::class,
                'fields' => ['code'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('emoticons', 'picture'),
                'type' => PictureColumnRenderer::class,
                'fields' => ['img'],
                'custom' => [
                    'pattern' => $this->applicationPath->getWebRoot() . 'uploads/emoticons/%s',
                ],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 10);
    }
}
