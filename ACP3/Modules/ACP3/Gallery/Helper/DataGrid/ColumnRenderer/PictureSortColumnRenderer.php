<?php
namespace ACP3\Modules\ACP3\Gallery\Helper\DataGrid\ColumnRenderer;


use ACP3\Core\Helpers\DataGrid\ColumnRenderer\AbstractColumnRenderer;
use ACP3\Core\Lang;
use ACP3\Core\Router;

/**
 * Class PictureSortColumnRenderer
 * @package ACP3\Modules\ACP3\Gallery\Helper\DataGrid\ColumnRenderer
 */
class PictureSortColumnRenderer extends AbstractColumnRenderer
{
    const NAME = 'picture_sort';

    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;

    /**
     * PictureSortColumnRenderer constructor.
     *
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
        $dbValue = $this->getValue($column, $dbResultRow);

        $value = '';
        if ($dbResultRow['last'] != $dbValue) {
            $value .= '<a href="' . $this->router->route('acp/gallery/pictures/order/id_' . $dbResultRow['id'] . '/action_down') . '"
                       title="' . $this->lang->t('system', 'move_down') . '"
                       data-ajax-form="true"
                       data-ajax-form-loading-text="' . $this->lang->t('system', 'loading_please_wait') . '"><i class="glyphicon glyphicon-arrow-down" aria-hidden="true"></i></a>';
        }
        if ($dbResultRow['first'] != $dbValue) {
            $value .= '<a href="' . $this->router->route('acp/gallery/pictures/order/id_' . $dbResultRow['id'] . '/action_up') . '"
                       title="' . $this->lang->t('system', 'move_up') . '"
                       data-ajax-form="true"
                       data-ajax-form-loading-text="' . $this->lang->t('system', 'loading_please_wait') . '"><i class="glyphicon glyphicon-arrow-up" aria-hidden="true"></i></a>';
        }
        if ($dbResultRow['first'] == $dbResultRow['last']) {
            $value = '<i class="glyphicon glyphicon-remove-circle text-danger text-danger" aria-hidden="true" title="' . $this->lang->t('system', 'move_impossible') . '"></i>';
        }

        $column['attribute']['data-order'] = $dbResultRow['pic'];

        return $this->render($column, $value);
    }
}