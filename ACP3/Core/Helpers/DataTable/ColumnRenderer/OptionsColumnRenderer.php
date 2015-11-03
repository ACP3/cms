<?php
namespace ACP3\Core\Helpers\DataTable\ColumnRenderer;

use ACP3\Core\Lang;
use ACP3\Core\Router;

/**
 * Class OptionsColumnRenderer
 * @package ACP3\Core\Helpers\DataTable\ColumnRenderer
 */
class OptionsColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;

    /**
     * @param \ACP3\Core\Lang   $lang
     * @param \ACP3\Core\Router $router
     */
    public function __construct(
        Lang $lang,
        Router $router
    )
    {
        $this->lang = $lang;
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function renderColumn(array $column, $dbResultRow = '', $type = self::TYPE_TD)
    {
        $value = '';

        $canEdit = $column['custom']['can_edit'];
        $resourcePathEdit = $column['custom']['resource_path_edit'];
        if ($canEdit) {
            $resourcePathEdit .= !preg_match('=/$=', $resourcePathEdit) ? '/' : '';
            $value .= '<a href="' . $this->router->route($resourcePathEdit . 'id_' . $dbResultRow['id']) . '" class="btn btn-default btn-xs btn-block">';
            $value .= '<i class="glyphicon glyphicon-edit"></i> ' . $this->lang->t('system', 'edit');
            $value .= '</a>';
        }

        $canDelete = $column['custom']['can_delete'];
        $resourcePathDelete = $column['custom']['resource_path_delete'];
        if ($canDelete) {
            $resourcePathDelete .= !preg_match('=/$=', $resourcePathDelete) ? '/' : '';
            $value .= ' <a href="' . $this->router->route($resourcePathDelete . 'entries_' . $dbResultRow['id']) . '" class="btn btn-danger btn-xs btn-block">';
            $value .= '<i class="glyphicon glyphicon-remove"></i> ' . $this->lang->t('system', 'delete');
            $value .= '</a>';
        }

        return parent::renderColumn($column, $value, $type);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'options';
    }
}