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
     * @var Core\Assets
     */
    protected $assets;
    /**
     * @var array
     */
    protected $alreadyIncluded = array();
    /**
     * @var string
     */
    protected $pluginName = 'js_libraries';

    public function __construct(Core\Assets $assets)
    {
        $this->assets = $assets;
    }

    /**
     * @param array $params
     * @return mixed|void
     */
    public function process(array $params)
    {
        $this->assets->enableJsLibraries(explode(',', $params['enable']));
    }
}