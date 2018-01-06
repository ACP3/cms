<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\View\Block\Admin;

use ACP3\Core;
use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\View\Block\AbstractDataGridBlock;
use ACP3\Core\View\Block\Context;
use ACP3\Modules\ACP3\Files\Helpers;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class FilesDataGridBlock extends AbstractDataGridBlock
{
    /**
     * @var Core\ACL\ACLInterface
     */
    private $acl;
    /**
     * @var Core\Settings\SettingsInterface
     */
    private $settings;

    /**
     * FilesDataGridBlock constructor.
     * @param Context\DataGridBlockContext $context
     * @param Core\ACL\ACLInterface $acl
     * @param Core\Settings\SettingsInterface $settings
     */
    public function __construct(
        Context\DataGridBlockContext $context,
        Core\ACL\ACLInterface $acl,
        Core\Settings\SettingsInterface $settings
    ) {
        parent::__construct($context);

        $this->acl = $acl;
        $this->settings = $settings;
    }

    /**
     * @inheritdoc
     */
    protected function addDataGridColumns(DataGrid $dataGrid)
    {
        $settings = $this->settings->getSettings($this->getModuleName());

        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('files', 'active'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer::class,
                'fields' => ['active'],
                'custom' => [
                    'search' => [0, 1],
                    'replace' => [$this->translator->t('system', 'no'), $this->translator->t('system', 'yes')],
                ],
            ], 60)
            ->addColumn([
                'label' => $this->translator->t('system', 'publication_period'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\DateColumnRenderer::class,
                'fields' => ['start', 'end'],
                'default_sort' => $settings['order_by'] === 'date',
                'default_sort_direction' => 'desc',
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('files', 'title'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['title'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('categories', 'category'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['cat'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('files', 'filesize'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['size'],
                'customer' => [
                    'default_value' => $this->translator->t('files', 'unknown_filesize'),
                ],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\RouteColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'custom' => [
                    'path' => Helpers::URL_KEY_PATTERN,
                ],
            ], 10);

        if ($this->acl->hasPermission('admin/files/index/sort') && $settings['order_by'] === 'custom') {
            $dataGrid
                ->addColumn([
                    'label' => $this->translator->t('system', 'order'),
                    'type' => Core\Helpers\DataGrid\ColumnRenderer\SortColumnRenderer::class,
                    'fields' => ['sort'],
                    'default_sort' => $settings['order_by'] === 'custom',
                    'custom' => [
                        'route_sort_down' => 'acp/files/index/sort/id_%d/action_down',
                        'route_sort_up' => 'acp/files/index/sort/id_%d/action_up',
                    ],
                ], 15);
        }
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $dataGrid = $this->getCurrentDataGrid();

        $this->configureDataGrid($dataGrid, [
            'ajax' => true,
            'identifier' => '#files-data-grid',
            'resource_path_delete' => 'admin/files/index/delete',
            'resource_path_edit' => 'admin/files/index/manage',
        ]);

        $grid = $dataGrid->render();
        if ($grid instanceof JsonResponse) {
            return $grid;
        }

        return [
            'grid' => $grid,
            'show_mass_delete_button' => $dataGrid->countDbResults() > 0,
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
