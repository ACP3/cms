<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core;

/**
 * Class JsLibraries
 * @package ACP3\Core\View\Renderer\Smarty
 */
class JsLibraries extends AbstractPlugin
{
    /**
     * @var Core\View
     */
    protected $view;
    /**
     * @var array
     */
    protected $alreadyIncluded = array();
    /**
     * @var string
     */
    protected $pluginName = 'js_libraries';

    public function __construct(Core\View $view)
    {
        $this->view = $view;
    }

    /**
     * @param $params
     *
     * @throws \Exception
     * @return string
     */
    public function process($params)
    {
        $this->view->enableJsLibraries(explode(',', $params['enable']));
    }
}