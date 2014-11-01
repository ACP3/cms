<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core;

/**
 * Class HasPermission
 * @package ACP3\Core\View\Renderer\Smarty
 */
class HasPermission extends AbstractPlugin
{
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var string
     */
    protected $pluginName = 'has_permission';

    public function __construct(Core\Modules $modules)
    {
        $this->modules = $modules;
    }

    /**
     * @param $params
     *
     * @return string
     */
    public function process($params)
    {
        if (isset($params['path']) === true) {
            return $this->modules->hasPermission($params['path']);
        } else {
            return false;
        }
    }
}