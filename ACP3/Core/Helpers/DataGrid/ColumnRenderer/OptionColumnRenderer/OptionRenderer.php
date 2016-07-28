<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionColumnRenderer;

use ACP3\Core\RouterInterface;

/**
 * Class OptionRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionColumnRenderer
 */
class OptionRenderer
{
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;
    /**
     * @var array
     */
    protected $options = [];

    /**
     * OptionRenderer constructor.
     *
     * @param \ACP3\Core\RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $route
     * @param string $translationPhrase
     * @param string $iconClass
     * @param string $buttonClass
     * @param bool   $useAjax
     */
    public function addOption($route, $translationPhrase, $iconClass, $buttonClass = 'btn-default', $useAjax = false)
    {
        $ajax = $useAjax === true ? ' data-ajax-form="true"' : '';
        $value = ' <a href="' . $this->router->route($route) . '" class="btn btn-xs ' . $buttonClass . '"' . $ajax . ' title="' . $translationPhrase . '">';
        $value .= '<i class="glyphicon ' . $iconClass . '"></i>';
        $value .= '<span class="sr-only">' . $translationPhrase . '</span>';
        $value .= '</a>';

        $this->options[] = $value;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function clearOptions()
    {
        $this->options = [];
    }
}
