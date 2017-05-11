<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\View\Block\Admin;

use ACP3\Core;
use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\View\Block\AbstractDataGridBlock;
use ACP3\Modules\ACP3\Newsletter\Helper\DataGrid\ColumnRenderer\AccountStatusColumnRenderer;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class NewsletterAccountsDataGridBlock extends AbstractDataGridBlock
{

    /**
     * @inheritdoc
     */
    protected function addDataGridColumns(DataGrid $dataGrid)
    {
        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('system', 'email_address'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['mail'],
                'default_sort' => true
            ], 60)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'salutation'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer::class,
                'fields' => ['salutation'],
                'custom' => [
                    'search' => [0, 1, 2],
                    'replace' => [
                        '',
                        $this->translator->t('newsletter', 'salutation_female'),
                        $this->translator->t('newsletter', 'salutation_male'),
                    ]
                ]
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'first_name'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['first_name'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'last_name'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['last_name'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'status'),
                'type' => AccountStatusColumnRenderer::class,
                'fields' => ['status'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true
            ], 10);
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $dataGrid = $this->getCurrentDataGrid();
        $this->configureDataGrid($dataGrid, [
            'ajax' => true,
            'identifier' => '#newsletter-accounts-data-grid',
            'resource_path_delete' => 'admin/newsletter/accounts/delete'
        ]);

        $grid = $dataGrid->render();
        if ($grid instanceof JsonResponse) {
            return $grid;
        }

        return [
            'grid' => $grid,
            'show_mass_delete_button' => $dataGrid->countDbResults() > 0
        ];
    }

    /**
     * @inheritdoc
     */
    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }
}
