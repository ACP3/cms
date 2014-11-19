<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;
use ACP3\Modules\Menus;

/**
 * Class Navbar
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class Navbar extends AbstractFunction
{
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var Menus\Helpers
     */
    protected $menuHelpers;

    /**
     * @param Core\Modules $modules
     * @param Menus\Helpers $menuHelpers
     */
    public function __construct(
        Core\Modules $modules,
        Menus\Helpers $menuHelpers
    )
    {
        $this->modules = $modules;
        $this->menuHelpers = $menuHelpers;
    }

    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'navbar';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        if ($this->modules->isActive('menus') === true) {
            return $this->menuHelpers->processNavbar(
                $params['block'],
                isset($params['use_bootstrap']) ? (bool)$params['use_bootstrap'] : true,
                !empty($params['class']) ? $params['class'] : '',
                !empty($params['dropdownItemClass']) ? $params['dropdownItemClass'] : '',
                !empty($params['tag']) ? $params['tag'] : 'ul',
                isset($params['itemTag']) ? $params['itemTag'] : 'li',
                !empty($params['dropdownWrapperTag']) ? $params['dropdownWrapperTag'] : 'li',
                !empty($params['classLink']) ? $params['classLink'] : '',
                !empty($params['inlineStyles']) ? $params['inlineStyles'] : ''
            );
        }
        return '';
    }
}