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
        $value = $this->getValue($column, $dbResultRow);

        if (isset($column['custom']['pattern']) &&
            $value !== null && $value !== $this->getDefaultValue($column)
        ) {
            $value = '<img src="' . $this->getUrl($column['custom'], $value) . '" alt="">';
        }

        return $this->render($column, $value);
    }

    /**
     * @param array  $data
     * @param string $value
     *
     * @return string
     */
    protected function getUrl(array $data, $value)
    {
        $url = sprintf($data['pattern'], $value);
        if (isset($data['isRoute'])) {
            return $this->router->route($url);
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'picture';
    }
}