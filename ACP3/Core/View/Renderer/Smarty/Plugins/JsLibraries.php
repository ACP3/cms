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
    protected $alreadyIncluded = [];
    /**
     * @var string
     */
    protected $pluginName = 'js_libraries';

    /**
     * @param Core\Assets $assets
     */
    public function __construct(Core\Assets $assets)
    {
        $this->assets = $assets;
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $this->assets->enableJsLibraries(explode(',', $params['enable']));
    }
}