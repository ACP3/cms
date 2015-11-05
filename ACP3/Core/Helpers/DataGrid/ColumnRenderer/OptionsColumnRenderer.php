<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\Lang;
use ACP3\Core\Router;

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
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $value = '';

        if ($column['custom']['can_edit']) {
            $resourcePathEdit = $column['custom']['resource_path_edit'];
            $resourcePathEdit .= !preg_match('=/$=', $resourcePathEdit) ? '/' : '';
            $value .= $this->renderOptionButton(
                $resourcePathEdit . 'id_' . $dbResultRow['id'],
                'edit',
                'glyphicon-edit',
                'btn-default'
            );
        }

        if ($column['custom']['can_delete']) {
            $resourcePathDelete = $column['custom']['resource_path_delete'];
            $resourcePathDelete .= !preg_match('=/$=', $resourcePathDelete) ? '/' : '';
            $value .= $this->renderOptionButton(
                $resourcePathDelete . 'entries_' . $dbResultRow['id'],
                'delete',
                'glyphicon-remove',
                'btn-danger'
            );
        }

        return $this->render($column, $value);
    }

    /**
     * @param string $route
     * @param string $translationPhrase
     * @param string $iconClass
     * @param string $buttonClass
     *
     * @return string
     */
    protected function renderOptionButton($route, $translationPhrase, $iconClass, $buttonClass)
    {
        $value = ' <a href="' . $this->router->route($route) . '" class="btn btn-xs btn-block ' . $buttonClass . '">';
        $value .= '<i class="glyphicon ' . $iconClass . '"></i> ' . $this->lang->t('system', $translationPhrase);
        $value .= '</a>';

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