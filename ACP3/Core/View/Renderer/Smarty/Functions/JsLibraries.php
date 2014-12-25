<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

/**
 * Class JsLibraries
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class JsLibraries extends AbstractFunction
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
     * @param Core\Assets $assets
     */
    public function __construct(Core\Assets $assets)
    {
        $this->assets = $assets;
    }

    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'js_libraries';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $this->assets->enableLibraries(explode(',', $params['enable']));
    }
}
