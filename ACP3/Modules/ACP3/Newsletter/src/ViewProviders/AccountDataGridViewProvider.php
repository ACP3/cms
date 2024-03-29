<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\ViewProviders;

use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Newsletter\DataGrid\ColumnRenderer\AccountStatusColumnRenderer;
use ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus;
use ACP3\Modules\ACP3\Newsletter\Repository\AccountDataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class AccountDataGridViewProvider
{
    public function __construct(private readonly DataGrid $dataGrid, private readonly AccountDataGridRepository $dataGridRepository, private readonly ResultsPerPage $resultsPerPage, private readonly Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>|JsonResponse
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
            ->setIdentifier('#newsletter-accounts-data-grid')
            ->setResourcePathDelete('admin/newsletter/accounts/delete')
            ->setQueryOptions(new QueryOption(
                'status',
                (string) AccountStatus::ACCOUNT_STATUS_DISABLED,
                'main',
                '!='
            ))
            ->addColumn([
                'label' => $this->translator->t('system', 'email_address'),
                'type' => TextColumnRenderer::class,
                'fields' => ['mail'],
                'default_sort' => true,
            ], 60)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'salutation'),
                'type' => ReplaceValueColumnRenderer::class,
                'fields' => ['salutation'],
                'custom' => [
                    'search' => [0, 1, 2],
                    'replace' => [
                        '',
                        $this->translator->t('newsletter', 'salutation_female'),
                        $this->translator->t('newsletter', 'salutation_male'),
                    ],
                ],
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'first_name'),
                'type' => TextColumnRenderer::class,
                'fields' => ['first_name'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'last_name'),
                'type' => TextColumnRenderer::class,
                'fields' => ['last_name'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'status'),
                'type' => AccountStatusColumnRenderer::class,
                'fields' => ['status'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 10);
    }
}
