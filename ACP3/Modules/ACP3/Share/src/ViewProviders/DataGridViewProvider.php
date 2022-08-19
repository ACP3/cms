<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\ViewProviders;

use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\RoundNumberColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\RouteColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\Enum\YesNoEnum;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Share\Repository\DataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataGridViewProvider
{
    public function __construct(private readonly DataGrid $dataGrid, private readonly DataGridRepository $dataGridRepository, private readonly ResultsPerPage $resultsPerPage, private readonly Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>|JsonResponse
     *
     * @throws \JsonException
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
            ->setIdentifier('#share-data-grid')
            ->setResourcePathDelete('admin/share/index/delete')
            ->setResourcePathEdit('admin/share/index/edit')
            ->addColumn([
                'label' => $this->translator->t('share', 'active'),
                'type' => ReplaceValueColumnRenderer::class,
                'fields' => ['active'],
                'custom' => [
                    'search' => YesNoEnum::values(),
                    'replace' => [$this->translator->t('system', 'no'), $this->translator->t('system', 'yes')],
                ],
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('share', 'uri'),
                'type' => RouteColumnRenderer::class,
                'fields' => ['uri'],
                'class' => 'w-100',
                'default_sort' => true,
                'custom' => [
                    'path' => '%s',
                ],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('share', 'average_rating'),
                'type' => RoundNumberColumnRenderer::class,
                'fields' => ['average_rating'],
                'custom' => [
                    'precision' => 2,
                ],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('share', 'ratings_count'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['ratings_count'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 10);
    }
}
