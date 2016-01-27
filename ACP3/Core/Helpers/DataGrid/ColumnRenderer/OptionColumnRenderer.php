<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Router;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class OptionColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class OptionColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;
    /**
     * @var \ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer
     */
    protected $optionRenderer;

    /**
     * @param \ACP3\Core\I18n\Translator                                                     $translator
     * @param \ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer $optionRenderer
     * @param \Symfony\Component\EventDispatcher\EventDispatcher                             $eventDispatcher
     */
    public function __construct(
        Translator $translator,
        OptionRenderer $optionRenderer,
        EventDispatcher $eventDispatcher
    )
    {
        $this->translator = $translator;
        $this->optionRenderer = $optionRenderer;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $this->eventDispatcher->dispatch(
            'data_grid.column_renderer.custom_option_before',
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $this->identifier)
        );

        if ($column['custom']['can_edit']) {
            $resourcePathEdit = $column['custom']['resource_path_edit'];
            $resourcePathEdit .= !preg_match('=/$=', $resourcePathEdit) ? '/' : '';
            $this->optionRenderer->addOption(
                $resourcePathEdit . 'id_' . $dbResultRow[$this->primaryKey],
                $this->translator->t('system', 'edit'),
                'glyphicon-edit',
                'btn-default'
            );
        }

        $this->eventDispatcher->dispatch(
            'data_grid.column_renderer.custom_option_between',
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $this->identifier)
        );

        if ($column['custom']['can_delete']) {
            $resourcePathDelete = $column['custom']['resource_path_delete'];
            $resourcePathDelete .= !preg_match('=/$=', $resourcePathDelete) ? '/' : '';
            $this->optionRenderer->addOption(
                $resourcePathDelete . 'entries_' . $dbResultRow[$this->primaryKey],
                $this->translator->t('system', 'delete'),
                'glyphicon-remove',
                'btn-danger'
            );
        }

        $this->eventDispatcher->dispatch(
            'data_grid.column_renderer.custom_option_after',
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $this->identifier)
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
}