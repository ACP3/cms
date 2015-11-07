<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;
use ACP3\Core\Router;

/**
 * Class PictureColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class PictureColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;

    /**
     * PictureColumnRenderer constructor.
     *
     * @param \ACP3\Core\Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        if (isset($column['custom']['pattern'])) {
            $dbValue = $this->getDbFieldValueIfExists($column, $dbResultRow);
            $value = '<img src="' . $this->getUrl($column['custom'], $dbValue) . '" alt="">';
        } else {
            $value = '';
        }

        return $this->render($column, $value);
    }

    /**
     * @param array  $custom
     * @param string $value
     *
     * @return string
     */
    protected function getUrl(array $custom, $value)
    {
        $value = sprintf($custom['pattern'], $value);
        if (isset($custom['isRoute'])) {
            return $this->router->route($value);
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'picture';
    }
}