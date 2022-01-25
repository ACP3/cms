<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\ViewProviders;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\DateColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\RouteColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\SortColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Files\Helpers;
use ACP3\Modules\ACP3\Files\Installer\Schema as FilesSchema;
use ACP3\Modules\ACP3\Files\Repository\DataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataGridViewProvider
{
    public function __construct(private ACL $acl, private DataGrid $dataGrid, private DataGridRepository $dataGridRepository, private ResultsPerPage $resultsPerPage, private SettingsInterface $settings, private Translator $translator)
    {
    }

    /**
     * @return array<string, array<string, mixed>>|JsonResponse
     *
     * @throws \JsonException
     */
    public function __invoke(): array|JsonResponse
    {
        return $this->dataGrid->render($this->configureDataGrid());
    }

    private function configureDataGrid(): Input
    {
        $settings = $this->settings->getSettings(FilesSchema::MODULE_NAME);

        $input = (new Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#files-data-grid')
            ->setResourcePathDelete('admin/files/index/delete')
            ->setResourcePathEdit('admin/files/index/edit')
            ->addColumn([
                'label' => $this->translator->t('files', 'active'),
                'type' => ReplaceValueColumnRenderer::class,
                'fields' => ['active'],
                'custom' => [
                    'search' => [0, 1],
                    'replace' => [$this->translator->t('system', 'no'), $this->translator->t('system', 'yes')],
                ],
            ], 60)
            ->addColumn([
                'label' => $this->translator->t('system', 'publication_period'),
                'type' => DateColumnRenderer::class,
                'fields' => ['start', 'end'],
                'default_sort' => $settings['order_by'] === 'date',
                'default_sort_direction' => 'desc',
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('files', 'title'),
                'type' => TextColumnRenderer::class,
                'fields' => ['title'],
                'class' => 'w-100',
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('files', 'filesize'),
                'type' => TextColumnRenderer::class,
                'fields' => ['size'],
                'customer' => [
                    'default_value' => $this->translator->t('files', 'unknown_filesize'),
                ],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => RouteColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'custom' => [
                    'path' => Helpers::URL_KEY_PATTERN,
                ],
            ], 10);

        if ($settings['order_by'] === 'custom' && $this->acl->hasPermission('admin/files/index/sort')) {
            $input
                ->addColumn([
                    'label' => $this->translator->t('system', 'order'),
                    'type' => SortColumnRenderer::class,
                    'fields' => ['sort'],
                    'class' => 'text-center',
                    'default_sort' => $settings['order_by'] === 'custom',
                    'custom' => [
                        'route_sort_down' => 'acp/files/index/sort/id_%d/action_down',
                        'route_sort_up' => 'acp/files/index/sort/id_%d/action_up',
                    ],
                ], 15);
        }

        return $input;
    }
}
