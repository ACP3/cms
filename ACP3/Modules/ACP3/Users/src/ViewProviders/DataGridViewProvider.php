<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\DataGrid\ColumnRenderer\RouteColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\Users\DataGrid\ColumnRenderer\UserRolesColumnRenderer;
use ACP3\Modules\ACP3\Users\Helpers;
use ACP3\Modules\ACP3\Users\Repository\DataGridRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataGridViewProvider
{
    public function __construct(private readonly DataGrid $dataGrid, private readonly DataGridRepository $dataGridRepository, private readonly ResultsPerPage $resultsPerPage, private readonly Translator $translator)
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
        return (new Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#users-data-grid')
            ->setResourcePathDelete('admin/users/index/delete')
            ->setResourcePathEdit('admin/users/index/edit')
            ->addColumn([
                'label' => $this->translator->t('users', 'nickname'),
                'type' => TextColumnRenderer::class,
                'fields' => ['nickname'],
                'class' => 'w-100',
                'default_sort' => true,
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'email_address'),
                'type' => TextColumnRenderer::class,
                'fields' => ['mail'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('permissions', 'roles'),
                'type' => UserRolesColumnRenderer::class,
                'fields' => ['id'],
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
    }
}
