<?php
namespace ACP3\Modules\ACP3\Newsletter\Helper\DataGrid\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\AbstractColumnRenderer;
use ACP3\Core\Lang;
use ACP3\Core\Router;

/**
 * Class AccountStatusColumnRenderer
 * @package ACP3\Modules\ACP3\Newsletter\Helper\DataGrid\ColumnRenderer
 */
class AccountStatusColumnRenderer extends AbstractColumnRenderer
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
     * AccountStatusColumnRenderer constructor.
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
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow, $identifier, $primaryKey)
    {
        $status = $this->getDbFieldValueIfExists($column, $dbResultRow);

        if ($status == 0) {
            $route = $this->router->route('acp/newsletter/accounts/activate/id_' . $dbResultRow['id']);
            $title = $this->lang->t('newsletter', 'activate_account');
            $value = '<a href="' . $route . '" title="' . $title . '">';
            $value .= '<i class="glyphicon glyphicon-remove text-danger"></i>';
            $value .= '</a>';
        } else {
            $value = '<i class="glyphicon glyphicon-ok text-success"></i>';
        }

        return $this->render($column, $value);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'account_status';
    }
}