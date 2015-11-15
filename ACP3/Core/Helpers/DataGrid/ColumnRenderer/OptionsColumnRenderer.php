<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionsColumnRenderer\OptionRenderer;
use ACP3\Core\Lang;
use ACP3\Core\Router;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class OptionsColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class OptionsColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;
    /**
     * @var \ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionsColumnRenderer\OptionRenderer
     */
    protected $optionRenderer;

    /**
     * @param \ACP3\Core\Lang                                                                 $lang
     * @param \ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionsColumnRenderer\OptionRenderer $optionRenderer
     * @param \Symfony\Component\EventDispatcher\EventDispatcher                              $eventDispatcher
     */
    public function __construct(
        Lang $lang,
        OptionRenderer $optionRenderer,
        EventDispatcher $eventDispatcher
    )
    {
        $this->lang = $lang;
        $this->optionRenderer = $optionRenderer;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow, $identifier, $primaryKey)
    {
        $this->eventDispatcher->dispatch(
            'data_grid.column_renderer.custom_option_before',
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $identifier)
        );

        if ($column['custom']['can_edit']) {
            $resourcePathEdit = $column['custom']['resource_path_edit'];
            $resourcePathEdit .= !preg_match('=/$=', $resourcePathEdit) ? '/' : '';
            $this->optionRenderer->addOption(
                $resourcePathEdit . 'id_' . $dbResultRow[$primaryKey],
                $this->lang->t('system', 'edit'),
                'glyphicon-edit',
                'btn-default'
            );
        }

        $this->eventDispatcher->dispatch(
            'data_grid.column_renderer.custom_option_between',
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $identifier)
        );

        if ($column['custom']['can_delete']) {
            $resourcePathDelete = $column['custom']['resource_path_delete'];
            $resourcePathDelete .= !preg_match('=/$=', $resourcePathDelete) ? '/' : '';
            $this->optionRenderer->addOption(
                $resourcePathDelete . 'entries_' . $dbResultRow[$primaryKey],
                $this->lang->t('system', 'delete'),
                'glyphicon-remove',
                'btn-danger'
            );
        }

        $this->eventDispatcher->dispatch(
            'data_grid.column_renderer.custom_option_after',
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $identifier)
        );

        return $this->render($column, $this->collectOptions());
    }

    /**
     * @return string
     */
    protected function collectOptions()
    {
        $value = '';

        foreach ($this->optionRenderer->getOptions() as $option) {
            $value .= $option;
        }

        $this->optionRenderer->clearOptions();

        return $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'options';
    }
}